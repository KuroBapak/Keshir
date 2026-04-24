<?php

namespace App\Services\Chatbot;

use App\Models\Discount;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Table;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaChatService
{
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ollama.url', 'http://127.0.0.1:11434'), '/');
        $this->model = config('services.ollama.model', 'llama3.1');
    }

    /**
     * Send a message to Ollama with function calling support and role context.
     */
    public function chat(string $userMessage, array $conversationHistory = [], string $role = 'customer'): array
    {
        try {
            $messages = $this->buildMessages($userMessage, $conversationHistory, $role);
            $tools = $this->getToolDeclarations($role);

            // Try with tools first; if model doesn't support tools, fallback to no-tools
            $response = $this->callOllama($messages, $tools);

            // Fallback: model doesn't support tool calling (400 error)
            if (!$response['success'] && ($response['status'] ?? 0) === 400) {
                Log::debug('Ollama model does not support tools, falling back to no-tools mode.');
                // Re-build messages with enriched prompt (inject tool data directly)
                $messages = $this->buildMessages($userMessage, $conversationHistory, $role, true);
                $response = $this->callOllama($messages, []);
            }

            if (!$response['success']) {
                return $response;
            }

            $data = $response['data'];
            $assistantMessage = $data['message'] ?? [];

            // Check if Ollama wants to call a tool
            if (!empty($assistantMessage['tool_calls'])) {
                // Fix empty arguments for JSON encoding to Ollama (PHP [] -> JSON [])
                foreach ($assistantMessage['tool_calls'] as &$tc) {
                    if (empty($tc['function']['arguments'])) {
                        $tc['function']['arguments'] = new \stdClass();
                    }
                }
                unset($tc);

                $toolCall = $assistantMessage['tool_calls'][0];
                $functionName = $toolCall['function']['name'];
                $functionArgs = (array) ($toolCall['function']['arguments'] ?? []);

                // Execute the function
                $functionResult = $this->executeFunctionCall($functionName, $functionArgs);

                // Append the assistant's tool_call message
                $messages[] = $assistantMessage;

                // Append the tool result as a "tool" role message
                $messages[] = [
                    'role' => 'tool',
                    'content' => json_encode($functionResult, JSON_UNESCAPED_UNICODE),
                ];

                // Call Ollama again with the tool result (no tools this time to force text response)
                $finalResponse = $this->callOllama($messages, []);

                if ($finalResponse['success']) {
                    $finalText = $finalResponse['data']['message']['content'] ?? '';
                    return [
                        'success' => true,
                        'message' => $finalText,
                        'function_called' => $functionName,
                    ];
                }

                return $finalResponse;
            }

            // No tool call — direct text response
            $textResponse = $assistantMessage['content'] ?? 'Maaf, saya tidak bisa memproses permintaan Anda saat ini.';

            // Perbaiki halusinasi Llama yang mengeluarkan JSON mentah di konten teks
            $trimmedResponse = trim($textResponse);
            if (is_string($trimmedResponse) && str_starts_with($trimmedResponse, '{') && str_ends_with($trimmedResponse, '}')) {
                $decoded = json_decode($trimmedResponse, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['name'])) {
                    $functionName = $decoded['name'];
                    $functionArgs = $decoded['parameters'] ?? [];
                    if (is_string($functionArgs)) {
                        $functionArgs = json_decode($functionArgs, true) ?? [];
                    }

                    $functionResult = $this->executeFunctionCall($functionName, $functionArgs);

                    $messages[] = [
                        'role' => 'tool',
                        'content' => json_encode($functionResult, JSON_UNESCAPED_UNICODE),
                    ];

                    $finalResponse = $this->callOllama($messages, []);

                    if ($finalResponse['success']) {
                        $finalText = $finalResponse['data']['message']['content'] ?? '';
                        return [
                            'success' => true,
                            'message' => $finalText,
                            'function_called' => $functionName . ' (hallucinated)',
                        ];
                    }
                }
            }

            return ['success' => true, 'message' => $textResponse];

        } catch (\Exception $e) {
            Log::error('OllamaChatService error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return [
                'success' => false,
                'message' => 'Maaf, terjadi gangguan pada layanan chatbot. Silakan coba lagi nanti atau hubungi staff kami. 🙏',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function buildMessages(string $userMessage, array $conversationHistory, string $role, bool $enriched = false): array
    {
        $messages = [];

        // System prompt as the first message
        $messages[] = [
            'role' => 'system',
            'content' => $this->getSystemPrompt($role, $enriched),
        ];

        // Append conversation history
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content'],
            ];
        }

        // Append the new user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    private function callOllama(array $messages, array $tools): array
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/api/chat", $payload);

        if (!$response->successful()) {
            $errorBody = $response->body();
            
            // Do not log ERROR if it's a 400 Bad Request caused by tool compatibility,
            // because the main chat() method will handle the fallback gracefully.
            if (!($response->status() === 400 && !empty($tools))) {
                Log::error('Ollama API error', ['status' => $response->status(), 'body' => $errorBody]);
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => 'Maaf, terjadi gangguan pada layanan AI lokal. Pastikan Ollama sedang berjalan. 🙏',
                'error' => $errorBody,
            ];
        }

        return ['success' => true, 'data' => $response->json()];
    }

    // =========================================================================
    // SYSTEM PROMPT & TOOL DECLARATIONS
    // =========================================================================

    private function getSystemPrompt(string $role, bool $enriched = false): string
    {
        $persona = $role === 'cashier'
            ? "Kamu adalah AI asisten operasional kasir/dapur untuk **Keshir Coffee Shop**. Kamu bertugas membantu staf internal. Kamu memiliki akses ke stok dapur dan data resep."
            : "Kamu adalah AI asisten virtual pelayan untuk **Keshir Coffee Shop**, sebuah kafe modern yang menyajikan berbagai minuman dan makanan ringan.";

        // Inject real menu data from database so AI doesn't hallucinate
        $menuData = $this->getMenuDataForPrompt();
        $settingsData = $this->getSettingsDataForPrompt();

        // When enriched mode (model doesn't support tool calling), inject extra data
        $extraData = '';
        if ($enriched) {
            $discountResult = $this->getActiveDiscounts();
            $tableResult = $this->getAvailableTables();
            $bestSellerResult = $this->getBestSellers(5);

            $extraData .= "\nDATA PROMO/DISKON AKTIF:\n";
            if (!empty($discountResult['data'])) {
                foreach ($discountResult['data'] as $d) {
                    $extraData .= "- {$d['name']}: {$d['type']} {$d['value']}\n";
                }
            } else {
                $extraData .= "- Tidak ada promo aktif saat ini.\n";
            }

            $extraData .= "\nDATA KETERSEDIAAN MEJA:\n";
            $extraData .= $tableResult['message'] . "\n";
            if (!empty($tableResult['available_tables'])) {
                foreach ($tableResult['available_tables'] as $t) {
                    $extraData .= "- Meja {$t['table_number']} (kapasitas: {$t['capacity']})\n";
                }
            }

            $extraData .= "\nDATA MENU TERLARIS:\n";
            if (!empty($bestSellerResult['data'])) {
                foreach ($bestSellerResult['data'] as $bs) {
                    $extraData .= "- #{$bs['rank']} {$bs['name']} ({$bs['category']}) - {$bs['price']} (terjual {$bs['total_sold']}x)\n";
                }
            } else {
                $extraData .= "- Belum ada data penjualan.\n";
            }
        }

        return <<<PROMPT
{$persona}

TENTANG DIRIMU:
- Kamu adalah chatbot AI yang terhubung langsung ke DATABASE Keshir Coffee Shop.
- Semua data menu, harga, varian, addon, resep/bahan, pajak, dan layanan yang kamu ketahui berasal dari DATABASE SISTEM, bukan dari pengetahuan umummu.
- Jika ditanya "dari mana kamu tahu?", jawab: "Saya membaca langsung dari database sistem Keshir Coffee Shop."
- Tujuanmu: membantu pelanggan melihat menu, mengetahui harga, varian, bahan/komposisi, pajak, rekomendasi, promo, ketersediaan meja, dan informasi lain seputar Keshir Coffee Shop.

Berikut adalah SEMUA menu yang tersedia di Keshir Coffee Shop saat ini (dari database):
{$menuData}

{$settingsData}
{$extraData}
INFO OPERASIONAL:
- Jam Buka: Senin - Jumat: 08.00 - 22.00 WIB
- Jam Buka: Sabtu - Minggu: 09.00 - 23.00 WIB
- Estimasi waktu penyajian: 5-10 menit untuk minuman, 10-15 menit untuk makanan.
- Untuk pesanan banyak (>5 item), estimasi bisa lebih lama sekitar 15-25 menit.

ATURAN WAJIB:
1. Jawab menggunakan BAHASA INDONESIA NATURAL yang ramah dan sopan, tambahkan emoji ☕🍰.
2. JANGAN PERNAH memberikan respons berupa format JSON, kode fungsi, atau data mentah. Selalu jawab dengan kalimat manusia biasa.
3. Jika pelanggan bertanya daftar menu / "ada menu apa saja", langsung tampilkan SEMUA menu dari daftar di atas.
4. DILARANG KERAS mengarang atau menambah data yang TIDAK ADA di database:
   - JANGAN mengarang bahan/ingredients jika data resep tidak tersedia di database.
   - JANGAN mengarang harga, varian, atau addon yang tidak ada.
   - Jika data tidak tersedia, jawab jujur: "Maaf, data tersebut belum tersedia di sistem kami."
5. Format harga: Rp 10.000.
6. Pertanyaan tentang pajak, biaya layanan, diskon, dan pembayaran adalah KONTEKS CAFE - jawab dengan data dari database.
7. Jika pelanggan bertanya benar-benar di luar konteks cafe (misal: coding, politik, sejarah umum), tolak dengan sopan.
PROMPT;
    }

    private function getSettingsDataForPrompt(): string
    {
        $taxEnabled = \App\Models\Setting::getValue('tax_enabled', '0') === '1';
        $taxRate = $taxEnabled ? \App\Models\Setting::getValue('tax_rate', '11') : 0;
        
        $serviceEnabled = \App\Models\Setting::getValue('service_charge_enabled', '0') === '1';
        $serviceRate = $serviceEnabled ? \App\Models\Setting::getValue('service_charge_rate', '5') : 0;

        $lines = ["INFO TAMBAHAN (Pajak & Layanan):"];
        if ($taxEnabled) {
            $lines[] = "- Pajak (Tax): {$taxRate}% dari total pesanan.";
        } else {
            $lines[] = "- Tidak ada pajak (Tax 0%).";
        }

        if ($serviceEnabled) {
            $lines[] = "- Biaya Layanan (Service Charge): {$serviceRate}% dari total pesanan.";
        } else {
            $lines[] = "- Tidak ada Biaya Layanan.";
        }

        return implode("\n", $lines);
    }

    /**
     * Build a text summary of all active menu items for the system prompt.
     */
    private function getMenuDataForPrompt(): string
    {
        try {
            $products = Product::where('is_active', true)
                ->with(['category', 'variants', 'addons'])
                ->get();

            if ($products->isEmpty()) {
                return "Belum ada menu yang terdaftar.";
            }

            $lines = [];
            foreach ($products as $p) {
                $line = "- {$p->name} | Kategori: " . ($p->category->name ?? '-') . " | Harga: Rp " . number_format($p->base_price, 0, ',', '.');

                if ($p->variants && $p->variants->isNotEmpty()) {
                    $variantParts = $p->variants->map(fn($v) => $v->variant_name . ' (+Rp ' . number_format($v->additional_price, 0, ',', '.') . ')')->toArray();
                    $line .= ' | Varian: ' . implode(', ', $variantParts);
                }

                if ($p->addons && $p->addons->isNotEmpty()) {
                    $addonParts = $p->addons->map(fn($a) => $a->addon_name . ' (+Rp ' . number_format($a->price, 0, ',', '.') . ')')->toArray();
                    $line .= ' | Addon: ' . implode(', ', $addonParts);
                }

                if ($p->description) {
                    $line .= ' | Deskripsi: ' . $p->description;
                }

                $lines[] = $line;
            }

            return implode("\n", $lines);
        } catch (\Exception $e) {
            return "Data menu tidak tersedia saat ini.";
        }
    }

    /**
     * Build tool declarations in Ollama's expected format.
     */
    private function getToolDeclarations(string $role): array
    {
        $allTools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_best_sellers',
                    'description' => 'Mengambil daftar menu terlaris/terpopuler berdasarkan data penjualan. Gunakan ketika pelanggan bertanya tentang menu favorit, paling laris, paling banyak dipesan, atau rekomendasi populer.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'limit' => [
                                'type' => 'integer',
                                'description' => 'Jumlah menu terlaris yang ditampilkan (default 5, max 10)',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_weather_recommendation',
                    'description' => 'Memberikan rekomendasi menu berdasarkan kondisi cuaca. Gunakan ketika pelanggan meminta rekomendasi berdasarkan cuaca, suhu, atau kondisi alam.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'condition' => [
                                'type' => 'string',
                                'description' => 'Kondisi cuaca: "hot" (panas/cerah), "cold" (dingin/sejuk), "rainy" (hujan/mendung), "normal" (biasa)',
                            ],
                        ],
                        'required' => ['condition'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_menu_details',
                    'description' => 'Mengambil detail lengkap sebuah menu termasuk deskripsi, harga, bahan/komposisi, varian, dan addon. Gunakan ketika pelanggan bertanya detail menu, komposisi, harga, atau varian.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'item_name' => [
                                'type' => 'string',
                                'description' => 'Nama menu yang dicari (bisa partial, misal "latte")',
                            ],
                        ],
                        'required' => ['item_name'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'check_stock_status',
                    'description' => 'Mengecek status stok bahan baku (ingredient). Gunakan ketika staff bertanya tentang ketersediaan stok bahan.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'ingredient_name' => [
                                'type' => 'string',
                                'description' => 'Nama bahan baku yang dicek (bisa partial, misal "milk")',
                            ],
                        ],
                        'required' => ['ingredient_name'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_active_discounts',
                    'description' => 'Mengambil daftar promo/diskon yang sedang aktif. Gunakan ketika pelanggan bertanya tentang promo, diskon, potongan harga, atau penawaran spesial.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new \stdClass(),
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_available_tables',
                    'description' => 'Mengecek meja yang tersedia untuk dine-in. Gunakan ketika pelanggan bertanya tentang ketersediaan meja, tempat duduk, atau mau makan di tempat.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new \stdClass(),
                    ],
                ],
            ],
        ];

        // Filter out stock lookup for customers
        if ($role === 'customer') {
            $allTools = array_values(array_filter($allTools, fn($t) => $t['function']['name'] !== 'check_stock_status'));
        }

        return $allTools;
    }

    private function executeFunctionCall(string $functionName, array $args): array
    {
        return match ($functionName) {
            'get_best_sellers' => $this->getBestSellers($args['limit'] ?? 5),
            'get_weather_recommendation' => $this->getWeatherRecommendation($args['condition'] ?? 'normal'),
            'get_menu_details' => $this->getMenuDetails($args['item_name'] ?? ''),
            'check_stock_status' => $this->checkStockStatus($args['ingredient_name'] ?? ''),
            'get_active_discounts' => $this->getActiveDiscounts(),
            'get_available_tables' => $this->getAvailableTables(),
            'get_tax', 'get_info' => ['message' => $this->getSettingsDataForPrompt()],
            default => ['error' => 'Unknown function: ' . $functionName],
        };
    }

    // =========================================================================
    // FUNCTION IMPLEMENTATIONS
    // =========================================================================

    private function getBestSellers(int $limit = 5): array
    {
        $limit = min(max($limit, 1), 10);

        $bestSellers = TransactionDetail::query()
            ->select('product_id')
            ->selectRaw('SUM(qty) as total_sold')
            ->where('status', 'done')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        if ($bestSellers->isEmpty()) {
            return ['message' => 'Belum ada data penjualan.', 'data' => []];
        }

        $data = $bestSellers->map(function ($item, $index) {
            $product = Product::with('category')->find($item->product_id);
            return [
                'rank' => $index + 1,
                'name' => $product->name,
                'category' => $product->category->name ?? '-',
                'price' => 'Rp ' . number_format($product->base_price, 0, ',', '.'),
                'total_sold' => (int) $item->total_sold,
                'description' => $product->description,
                'image_url' => $product->image_url,
            ];
        })->values()->toArray();

        return ['message' => "Berikut {$limit} menu terlaris kami:", 'data' => $data];
    }

    private function getWeatherRecommendation(string $condition): array
    {
        $condition = strtolower(trim($condition));

        if (in_array($condition, ['hot', 'panas', 'cerah', 'terik'])) {
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('tags', 'like', '%refreshing%')
                      ->orWhere('name', 'like', '%Iced%')
                      ->orWhere('name', 'like', '%Es %')
                      ->orWhere('name', 'like', '%Frappe%');
                })->with('category')->get();
            $weather = 'Cuaca panas ☀️';
            $suggestion = 'Minuman dingin dan menyegarkan cocok untuk cuaca panas!';
        } elseif (in_array($condition, ['cold', 'dingin', 'sejuk', 'rainy', 'hujan', 'gerimis', 'mendung'])) {
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', 'Hot%')
                      ->orWhere('name', 'Espresso')
                      ->orWhere('name', 'Americano')
                      ->orWhere('name', 'Cappuccino')
                      ->orWhere('name', 'Latte')
                      ->orWhere('name', 'Mocha')
                      ->orWhere('name', 'like', '%Croissant');
                })->with('category')->get();
            $weather = 'Cuaca dingin/hujan 🌧️';
            $suggestion = 'Minuman hangat sempurna untuk menemani cuaca dingin!';
        } else {
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('tags', 'like', '%recommended%')
                      ->orWhere('tags', 'like', '%trending%');
                })->with('category')->get();
            $weather = 'Cuaca normal 🌤️';
            $suggestion = 'Berikut menu rekomendasi spesial dari kami!';
        }

        $data = $products->map(fn($p) => [
            'name' => $p->name,
            'category' => $p->category->name ?? '-',
            'price' => 'Rp ' . number_format($p->base_price, 0, ',', '.'),
            'description' => $p->description,
            'image_url' => $p->image_url,
        ])->values()->toArray();

        return [
            'weather' => $weather,
            'suggestion' => $suggestion,
            'recommended_items' => $data,
            'total_recommendations' => count($data),
        ];
    }

    private function getMenuDetails(string $itemName): array
    {
        if (empty($itemName)) {
            return ['error' => 'Nama menu tidak boleh kosong.'];
        }

        $products = Product::where('is_active', true)
            ->where('name', 'like', "%{$itemName}%")
            ->with(['category', 'recipe.details.ingredient', 'variants', 'addons'])
            ->get();

        if ($products->isEmpty()) {
            $allProducts = Product::where('is_active', true)->pluck('name')->toArray();
            return [
                'message' => "Menu '{$itemName}' tidak ditemukan.",
                'suggestion' => 'Menu yang tersedia: ' . implode(', ', array_slice($allProducts, 0, 10)) . '...',
            ];
        }

        $data = $products->map(function ($product) {
            $result = [
                'name' => $product->name,
                'category' => $product->category->name ?? '-',
                'base_price' => 'Rp ' . number_format($product->base_price, 0, ',', '.'),
                'description' => $product->description,
                'tags' => $product->tags,
                'image_url' => $product->image_url,
            ];

            if ($product->recipe && $product->recipe->details->isNotEmpty()) {
                $result['ingredients'] = $product->recipe->details->map(fn($d) => [
                    'name' => $d->ingredient->name,
                    'quantity' => $d->quantity,
                    'unit' => $d->ingredient->unit,
                ])->toArray();
            } else {
                $result['ingredients'] = [];
                $result['ingredients_note'] = 'Data resep/bahan untuk menu ini BELUM dimasukkan ke dalam database. JANGAN mengarang bahan.';
            }

            if ($product->variants->isNotEmpty()) {
                $result['variants'] = $product->variants->map(fn($v) => [
                    'name' => $v->variant_name,
                    'additional_price' => $v->additional_price > 0
                        ? '+Rp ' . number_format($v->additional_price, 0, ',', '.')
                        : 'Gratis',
                ])->toArray();
            }

            if ($product->addons->isNotEmpty()) {
                $result['addons'] = $product->addons->map(fn($a) => [
                    'name' => $a->addon_name,
                    'price' => '+Rp ' . number_format($a->price, 0, ',', '.'),
                ])->toArray();
            }

            return $result;
        })->toArray();

        return [
            'message' => count($data) === 1
                ? "Detail menu {$data[0]['name']}:"
                : 'Ditemukan ' . count($data) . ' menu yang cocok:',
            'data' => $data,
        ];
    }

    private function checkStockStatus(string $ingredientName): array
    {
        if (empty($ingredientName)) {
            return ['error' => 'Nama bahan tidak boleh kosong.'];
        }

        $ingredients = Ingredient::where('name', 'like', "%{$ingredientName}%")->get();

        if ($ingredients->isEmpty()) {
            return [
                'message' => "Bahan '{$ingredientName}' tidak ditemukan.",
                'available_ingredients' => Ingredient::pluck('name')->take(15)->toArray(),
            ];
        }

        $data = $ingredients->map(function ($ing) {
            $status = 'Aman ✅';
            if ($ing->total_stock <= 0) $status = 'HABIS ❌';
            elseif ($ing->isBelowMinimum()) $status = 'STOK RENDAH ⚠️';

            return [
                'name' => $ing->name,
                'total_stock' => $ing->total_stock,
                'unit' => $ing->unit,
                'minimum_stock' => $ing->minimum_stock,
                'status' => $status,
                'is_below_minimum' => $ing->isBelowMinimum(),
            ];
        })->toArray();

        return [
            'message' => count($data) === 1
                ? "Status stok {$data[0]['name']}:"
                : 'Ditemukan ' . count($data) . ' bahan yang cocok:',
            'data' => $data,
        ];
    }

    private function getActiveDiscounts(): array
    {
        $discounts = Discount::where('is_active', true)->get();

        if ($discounts->isEmpty()) {
            return ['message' => 'Saat ini tidak ada promo/diskon yang aktif.', 'data' => []];
        }

        $data = $discounts->map(fn($d) => [
            'name' => $d->name,
            'type' => $d->type === 'percentage' ? 'Persentase' : 'Potongan Harga',
            'value' => $d->type === 'percentage'
                ? $d->value . '%'
                : 'Rp ' . number_format($d->value, 0, ',', '.'),
        ])->toArray();

        return [
            'message' => 'Berikut promo/diskon yang sedang aktif:',
            'data' => $data,
            'total' => count($data),
        ];
    }

    private function getAvailableTables(): array
    {
        $tables = Table::all();

        if ($tables->isEmpty()) {
            return ['message' => 'Data meja belum dikonfigurasi.', 'data' => []];
        }

        $available = $tables->where('status', 'available');
        $total = $tables->count();

        $data = $available->map(fn($t) => [
            'table_number' => $t->table_number,
            'capacity' => $t->capacity . ' orang',
        ])->values()->toArray();

        return [
            'message' => count($data) > 0
                ? 'Ada ' . count($data) . ' meja tersedia dari total ' . $total . ' meja:'
                : 'Maaf, semua meja sedang terisi saat ini. Total meja: ' . $total,
            'available_tables' => $data,
            'total_tables' => $total,
            'available_count' => count($data),
        ];
    }
}
