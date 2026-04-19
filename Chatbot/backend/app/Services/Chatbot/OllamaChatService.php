<?php

namespace App\Services\Chatbot;

use App\Models\Product;
use App\Models\Ingredient;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaChatService
{
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/');
        $this->model = env('OLLAMA_MODEL', 'llama3.1');
    }

    /**
     * Send a message to Ollama with function calling support and role context.
     */
    public function chat(string $userMessage, array $conversationHistory = [], string $role = 'customer'): array
    {
        try {
            $messages = $this->buildMessages($userMessage, $conversationHistory, $role);
            $tools = $this->getToolDeclarations($role);

            $response = $this->callOllama($messages, $tools);

            if (!$response['success']) {
                return $response;
            }

            $data = $response['data'];
            $assistantMessage = $data['message'] ?? [];

            // Check if Ollama wants to call a tool
            if (!empty($assistantMessage['tool_calls'])) {
                $toolCall = $assistantMessage['tool_calls'][0];
                $functionName = $toolCall['function']['name'];
                $functionArgs = $toolCall['function']['arguments'] ?? [];

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

    private function buildMessages(string $userMessage, array $conversationHistory, string $role): array
    {
        $messages = [];

        // System prompt as the first message
        $messages[] = [
            'role' => 'system',
            'content' => $this->getSystemPrompt($role),
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
            Log::error('Ollama API error', ['status' => $response->status(), 'body' => $errorBody]);

            return [
                'success' => false,
                'message' => 'Maaf, terjadi gangguan pada layanan AI lokal. Pastikan Ollama sedang berjalan. 🙏',
                'error' => $errorBody,
            ];
        }

        return ['success' => true, 'data' => $response->json()];
    }

    // =========================================================================
    // SYSTEM PROMPT & TOOL DECLARATIONS
    // =========================================================================

    private function getSystemPrompt(string $role): string
    {
        $persona = $role === 'cashier'
            ? "Kamu adalah asisten operasional kasir/dapur untuk **Keshir Coffee Shop**. Kamu bertugas membantu staf internal. Kamu memiliki akses ke stok dapur dan data resep."
            : "Kamu adalah asisten virtual / pelayan untuk **Keshir Coffee Shop**, sebuah kafe modern yang menyajikan berbagai minuman dan makanan ringan.";

        return <<<PROMPT
{$persona}

**Panduan Perilaku Utama:**
- Gunakan bahasa Indonesia yang ramah, santai, dan profesional.
- Tambahkan emoji yang relevan untuk membuat percakapan lebih hidup ☕🍰
- Format harga dalam Rupiah (contoh: Rp 25.000).
- Jangan mengarang data menu atau harga — selalu gunakan fungsi/tool yang tersedia.
- Jika ada pelanggan bertanya di luar konteks café, tolak menjawab dengan sopan.

**ATURAN WAJIB MENAMPILKAN GAMBAR (PENTING):**
Setiap kali kamu merekomendasikan atau menyebutkan detail sebuah menu dan data balasan dari function memiliki nilai `image_url`, kamu **WAJIB** menampilkannya menggunakan format Markdown gambar: `![Nama Menu](image_url)` di atas nama menu tersebut. Contoh balasan yang benar:
"Tentu, ini rekomendasi spesial kami:
![Caramel Macchiato](/example-photo.jpg)
**Caramel Macchiato** - Rp 38.000..."
PROMPT;
    }

    /**
     * Build tool declarations in Ollama's expected format.
     * Ollama uses standard OpenAI-compatible tool format.
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
            default => ['error' => 'Unknown function: ' . $functionName],
        };
    }

    // =========================================================================
    // FUNCTION IMPLEMENTATIONS (unchanged from GeminiChatService)
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
}
