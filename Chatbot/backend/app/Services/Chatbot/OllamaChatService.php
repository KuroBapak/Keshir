<?php

namespace App\Services\Chatbot;

use App\Models\Product;
use App\Models\Ingredient;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaChatService
{
    private string $baseUrl;
    private string $model;

    // BMKG API config — default: Cikarang Utara, Kab. Bekasi, Jawa Barat
    private string $bmkgAdm4Code = '32.16.09.2004';

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
            // Check if the user's question maps to a known function
            $detectedFunction = $this->detectQuestionIntent($userMessage);

            // If we detect a clear function intent, execute it directly
            if ($detectedFunction) {
                Log::info("Direct function intent detected: {$detectedFunction['name']}");
                $functionResult = $this->executeFunctionCall($detectedFunction['name'], $detectedFunction['args']);

                // Try AI formatting with a SIMPLE prompt (no history, lightweight)
                $formatMessages = [
                    ['role' => 'system', 'content' => 'Kamu adalah Asisten Keshir Coffee Shop. Sampaikan data berikut kepada pelanggan dalam bahasa Indonesia yang ramah dan natural. Gunakan emoji ☕🍰. Format harga: Rp xx.xxx. JANGAN tampilkan JSON atau kode. Jawab langsung sebagai kalimat.'],
                    ['role' => 'user', 'content' => "Pelanggan bertanya: \"{$userMessage}\"\n\nData dari sistem:\n" . json_encode($functionResult, JSON_UNESCAPED_UNICODE)],
                ];

                $formatResponse = $this->callOllama($formatMessages, []);

                if ($formatResponse['success']) {
                    $text = trim($formatResponse['data']['message']['content'] ?? '');
                    if (!empty($text) && !str_starts_with($text, '{') && !str_starts_with($text, '[')) {
                        return ['success' => true, 'message' => $text, 'function_called' => $detectedFunction['name']];
                    }
                }

                // Fallback: format manually
                return [
                    'success' => true,
                    'message' => $this->formatFunctionResultAsText($detectedFunction['name'], $functionResult),
                    'function_called' => $detectedFunction['name'],
                ];
            }

            // For general questions, use enriched mode directly (no tool calling)
            $messages = $this->buildMessages($userMessage, $conversationHistory, $role, true);
            $response = $this->callOllama($messages, []);

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
<<<<<<< Updated upstream:Chatbot/backend/app/Services/Chatbot/OllamaChatService.php
=======

            // Detect hallucinated function calls (model outputs JSON instead of text)
            $functionName = $this->detectHallucinatedFunctionCall($textResponse);

            if ($functionName) {
                Log::info("Hallucinated function call detected: {$functionName}");

                // Extract args if possible
                $trimmed = trim($textResponse);
                $decoded = json_decode($trimmed, true);
                $functionArgs = ($decoded && isset($decoded['parameters'])) ? $decoded['parameters'] : [];
                if (is_string($functionArgs)) {
                    $functionArgs = json_decode($functionArgs, true) ?? [];
                }
                if (!is_array($functionArgs)) {
                    $functionArgs = [];
                }

                $functionResult = $this->executeFunctionCall($functionName, $functionArgs);

                // Try sending the result back to AI for natural language formatting
                $formatMessages = [
                    ['role' => 'system', 'content' => 'Kamu adalah asisten Keshir Coffee Shop. Sampaikan data berikut kepada pelanggan dalam bahasa Indonesia yang ramah. Gunakan emoji. JANGAN tampilkan JSON.'],
                    ['role' => 'user', 'content' => 'Tolong sampaikan ini: ' . json_encode($functionResult, JSON_UNESCAPED_UNICODE)],
                ];

                $finalResponse = $this->callOllama($formatMessages, []);

                if ($finalResponse['success']) {
                    $finalText = trim($finalResponse['data']['message']['content'] ?? '');

                    // If AI STILL outputs JSON or empty, format it ourselves
                    if (empty($finalText) || str_starts_with($finalText, '{') || str_starts_with($finalText, '[')) {
                        $finalText = $this->formatFunctionResultAsText($functionName, $functionResult);
                    }

                    return [
                        'success' => true,
                        'message' => $finalText,
                        'function_called' => $functionName,
                    ];
                }

                // If Ollama fails entirely, format the result ourselves (guaranteed no JSON)
                return [
                    'success' => true,
                    'message' => $this->formatFunctionResultAsText($functionName, $functionResult),
                    'function_called' => $functionName,
                ];
            }

>>>>>>> Stashed changes:app/Services/Chatbot/OllamaChatService.php
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

        // Limit conversation history to prevent context overflow on small models
        $maxHistory = 6;
        $history = array_slice($conversationHistory, -$maxHistory);

        // Append conversation history
        foreach ($history as $msg) {
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

        // Inject current weather context from BMKG
        $weather = $this->fetchCurrentWeatherFromBMKG();
        $weatherContext = "KONDISI CUACA SAAT INI (Sumber: BMKG - {$weather['source']}):\n";
        $weatherContext .= "- Lokasi: {$weather['location']}\n";
        $weatherContext .= "- Cuaca: {$weather['description']}\n";
        if ($weather['temperature']) {
            $weatherContext .= "- Suhu: {$weather['temperature']}°C\n";
        }
        if ($weather['humidity']) {
            $weatherContext .= "- Kelembapan: {$weather['humidity']}%\n";
        }
        $weatherContext .= "- Waktu Prakiraan: {$weather['forecast_time']}\n";
        $weatherContext .= "- Kondisi Internal: {$weather['condition']}";

        return <<<PROMPT
{$persona}

<<<<<<< Updated upstream:Chatbot/backend/app/Services/Chatbot/OllamaChatService.php
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
=======
TENTANG DIRIMU:
- Kamu adalah chatbot AI yang terhubung langsung ke DATABASE Keshir Coffee Shop.
- Semua data menu, harga, varian, addon, resep/bahan, pajak, dan layanan yang kamu ketahui berasal dari DATABASE SISTEM, bukan dari pengetahuan umummu.
- Jika ditanya "dari mana kamu tahu?", jawab: "Saya membaca langsung dari database sistem Keshir Coffee Shop."
- Tujuanmu: membantu pelanggan melihat menu, mengetahui harga, varian, bahan/komposisi, pajak, rekomendasi, promo, ketersediaan meja, dan informasi lain seputar Keshir Coffee Shop.
- JANGAN PERNAH menyebutkan bahwa kamu adalah AI, model bahasa, atau chatbot. Perkenalkan diri sebagai "Asisten Keshir".

Berikut adalah SEMUA menu yang tersedia di Keshir Coffee Shop saat ini (dari database):
{$menuData}

{$settingsData}

{$weatherContext}
{$extraData}
INFO OPERASIONAL:
- Lokasi: Cikarang Utara, Kabupaten Bekasi, Jawa Barat
- Jam Buka: Senin - Jumat: 08.00 - 22.00 WIB
- Jam Buka: Sabtu - Minggu: 09.00 - 23.00 WIB
- Metode Pembayaran: Cash & QRIS
- Layanan: Dine-in & Take Away
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
8. Saat pelanggan bertanya rekomendasi cuaca atau minuman yang cocok, gunakan tool `get_weather_recommendation` yang akan membaca data cuaca BMKG real-time. Sampaikan kondisi cuaca saat ini secara natural di dalam balasan, lalu sambungkan dengan rekomendasi menu.
9. Jika pelanggan bertanya tentang informasi toko (jam buka, alamat, metode pembayaran, pajak, dll), jawab berdasarkan data di atas. JANGAN mengarang informasi.

**ATURAN WAJIB MENAMPILKAN GAMBAR (PENTING):**
Setiap kali kamu merekomendasikan atau menyebutkan detail sebuah menu dan data balasan dari function memiliki nilai `image_url`, kamu **WAJIB** menampilkannya menggunakan format Markdown gambar: `![Nama Menu](image_url)` di atas nama menu tersebut.
>>>>>>> Stashed changes:app/Services/Chatbot/OllamaChatService.php
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
                    'description' => 'Memberikan rekomendasi menu berdasarkan kondisi cuaca BMKG real-time saat ini. SELALU gunakan tool ini ketika pelanggan meminta rekomendasi berdasarkan cuaca, suhu, suasana, atau bertanya "minum apa ya?" atau "enaknya minum apa?"',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'condition' => [
                                'type' => 'string',
                                'description' => 'Kondisi cuaca: "auto" (otomatis dari BMKG), "hot" (panas/cerah), "cold" (dingin/sejuk), "rainy" (hujan/mendung), "normal" (biasa). Gunakan "auto" jika pelanggan tidak menyebutkan cuaca spesifik.',
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
<<<<<<< Updated upstream:Chatbot/backend/app/Services/Chatbot/OllamaChatService.php
=======
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
>>>>>>> Stashed changes:app/Services/Chatbot/OllamaChatService.php
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
            'get_weather_recommendation' => $this->getWeatherRecommendation($args['condition'] ?? 'auto'),
            'get_menu_details' => $this->getMenuDetails($args['item_name'] ?? ''),
            'check_stock_status' => $this->checkStockStatus($args['ingredient_name'] ?? ''),
            default => ['error' => 'Unknown function: ' . $functionName],
        };
    }

    /**
     * Detect user question intent using keyword matching.
     * This bypasses AI tool-calling entirely for common queries.
     */
    private function detectQuestionIntent(string $message): ?array
    {
        $msg = strtolower($message);

        // Best sellers / terlaris
        if (preg_match('/(terlaris|best.?seller|populer|favorit|paling laris|paling banyak|rekomendasi menu)/i', $msg)) {
            return ['name' => 'get_best_sellers', 'args' => ['limit' => 5]];
        }

        // Weather recommendation
        if (preg_match('/(cuaca|hujan|panas|dingin|rekomendasi.*(minum|menu)|enaknya (minum|makan)|cocok.*minum|minum apa)/i', $msg)) {
            return ['name' => 'get_weather_recommendation', 'args' => ['condition' => 'auto']];
        }

        // Discount / promo
        if (preg_match('/(promo|diskon|discount|potongan|penawaran|voucher)/i', $msg)) {
            return ['name' => 'get_active_discounts', 'args' => []];
        }

        // Tables
        if (preg_match('/(meja|table|tempat duduk|dine.?in|makan di tempat|kursi)/i', $msg)) {
            return ['name' => 'get_available_tables', 'args' => []];
        }

        // Tax / service charge
        if (preg_match('/(pajak|tax|ppn|service charge|biaya layanan|berapa persen)/i', $msg)) {
            return ['name' => 'get_tax', 'args' => []];
        }

        // Menu details (specific item query)
        if (preg_match('/(detail|komposisi|bahan|ingredient|resep).*(menu|kopi|latte|cappuccino|mocha|espresso|americano|tea|matcha|frappe|croissant)/i', $msg, $matches)) {
            $itemName = $matches[2] ?? '';
            return ['name' => 'get_menu_details', 'args' => ['item_name' => $itemName]];
        }

        // Stock check (cashier only)
        if (preg_match('/(stok|stock|persediaan|bahan.*(habis|sisa|tinggal))/i', $msg)) {
            return ['name' => 'check_stock_status', 'args' => ['ingredient_name' => '']];
        }

        // No clear intent detected → let AI handle it
        return null;
    }

    /**
     * Detect if the AI's text response is actually a hallucinated function call.
     * Handles both valid JSON and malformed JSON (e.g., {"name": "func", "parameters": {""}}).
     * Returns the function name if detected, null otherwise.
     */
    private function detectHallucinatedFunctionCall(string $text): ?string
    {
        $text = trim($text);

        // List of valid function names
        $validFunctions = [
            'get_best_sellers',
            'get_weather_recommendation',
            'get_menu_details',
            'check_stock_status',
            'get_active_discounts',
            'get_available_tables',
            'get_tax',
            'get_info',
        ];

        // Method 1: Try valid JSON decode
        if (str_starts_with($text, '{')) {
            $decoded = json_decode($text, true);
            if ($decoded && isset($decoded['name']) && in_array($decoded['name'], $validFunctions)) {
                return $decoded['name'];
            }
        }

        // Method 2: Regex-based detection for malformed JSON
        foreach ($validFunctions as $fn) {
            if (str_contains($text, $fn)) {
                return $fn;
            }
        }

        return null;
    }

    /**
     * Last-resort formatter: convert function result arrays into
     * human-readable Indonesian text so raw JSON is NEVER shown.
     */
    private function formatFunctionResultAsText(string $functionName, array $result): string
    {
        $message = $result['message'] ?? '';

        return match ($functionName) {
            'get_best_sellers' => $this->formatBestSellers($result),
            'get_weather_recommendation' => $this->formatWeatherRecommendation($result),
            'get_menu_details' => $this->formatMenuDetails($result),
            'check_stock_status' => $this->formatStockStatus($result),
            'get_active_discounts' => $this->formatDiscounts($result),
            'get_available_tables' => $this->formatTables($result),
            'get_tax', 'get_info' => "💰 **Informasi Pajak & Biaya Layanan:**\n\n" . ($result['message'] ?? $this->getSettingsDataForPrompt()),
            default => $message ?: 'Informasi berhasil ditemukan. Silakan tanyakan lebih lanjut! ☕',
        };
    }

    private function formatBestSellers(array $result): string
    {
        $text = "⭐ **Menu Terlaris Keshir Coffee Shop:**\n\n";
        if (empty($result['data'])) return $text . "Belum ada data penjualan saat ini.";
        foreach ($result['data'] as $item) {
            $text .= "**{$item['rank']}. {$item['name']}** ({$item['category']})\n";
            $text .= "   💰 {$item['price']} — terjual {$item['total_sold']}x\n\n";
        }
        return $text;
    }

    private function formatWeatherRecommendation(array $result): string
    {
        $text = "🌤️ **Rekomendasi Menu Berdasarkan Cuaca**\n\n";
        $text .= "{$result['weather_label']} — {$result['suggestion']}\n\n";
        if (!empty($result['bmkg_realtime'])) {
            $bmkg = $result['bmkg_realtime'];
            $text .= "📍 {$bmkg['location']} | {$bmkg['description']} | {$bmkg['temperature']}\n\n";
        }
        if (empty($result['recommended_items'])) return $text . "Tidak ada rekomendasi khusus saat ini.";
        foreach ($result['recommended_items'] as $item) {
            $text .= "☕ **{$item['name']}** ({$item['category']}) — {$item['price']}\n";
        }
        return $text;
    }

    private function formatMenuDetails(array $result): string
    {
        if (!empty($result['error'])) return $result['error'];
        if (empty($result['data'])) return $result['message'] ?? 'Menu tidak ditemukan.';
        $text = "";
        foreach ($result['data'] as $item) {
            $text .= "📋 **{$item['name']}** ({$item['category']})\n";
            $text .= "💰 Harga: {$item['base_price']}\n";
            if (!empty($item['description'])) $text .= "📝 {$item['description']}\n";
            if (!empty($item['variants'])) {
                $text .= "🔹 Varian: " . implode(', ', array_map(fn($v) => "{$v['name']} ({$v['additional_price']})", $item['variants'])) . "\n";
            }
            if (!empty($item['addons'])) {
                $text .= "➕ Addon: " . implode(', ', array_map(fn($a) => "{$a['name']} ({$a['price']})", $item['addons'])) . "\n";
            }
            $text .= "\n";
        }
        return $text;
    }

    private function formatStockStatus(array $result): string
    {
        if (!empty($result['error'])) return $result['error'];
        if (empty($result['data'])) return $result['message'] ?? 'Bahan tidak ditemukan.';
        $text = "📦 **Status Stok:**\n\n";
        foreach ($result['data'] as $item) {
            $text .= "• {$item['name']}: {$item['total_stock']} {$item['unit']} — {$item['status']}\n";
        }
        return $text;
    }

    private function formatDiscounts(array $result): string
    {
        if (empty($result['data'])) return "ℹ️ Saat ini tidak ada promo atau diskon yang sedang aktif. Tapi jangan khawatir, menu kami tetap terjangkau! ☕";
        $text = "🎉 **Promo & Diskon Aktif:**\n\n";
        foreach ($result['data'] as $d) {
            $text .= "• **{$d['name']}**: {$d['type']} {$d['value']}\n";
        }
        return $text;
    }

    private function formatTables(array $result): string
    {
        $text = "🪑 {$result['message']}\n\n";
        if (!empty($result['available_tables'])) {
            foreach ($result['available_tables'] as $t) {
                $text .= "• Meja **{$t['table_number']}** — kapasitas {$t['capacity']}\n";
            }
        }
        return $text;
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

    /**
     * REFACTORED: Now fetches real-time weather from BMKG API
     * and uses it to filter menu recommendations.
     */
    private function getWeatherRecommendation(string $condition): array
    {
        // Fetch real-time weather from BMKG
        $bmkgWeather = $this->fetchCurrentWeatherFromBMKG();

        // If condition is "auto" or empty, use real BMKG data
        if (empty($condition) || $condition === 'auto') {
            $condition = $bmkgWeather['condition'];
        }

        $condition = strtolower(trim($condition));

        if (in_array($condition, ['hot', 'panas', 'cerah', 'terik'])) {
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('tags', 'like', '%refreshing%')
                      ->orWhere('name', 'like', '%Iced%')
                      ->orWhere('name', 'like', '%Es %')
                      ->orWhere('name', 'like', '%Frappe%');
                })->with('category')->get();
            $weatherLabel = 'Cuaca panas ☀️';
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
            $weatherLabel = 'Cuaca dingin/hujan 🌧️';
            $suggestion = 'Minuman hangat sempurna untuk menemani cuaca dingin!';
        } else {
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('tags', 'like', '%recommended%')
                      ->orWhere('tags', 'like', '%trending%');
                })->with('category')->get();
            $weatherLabel = 'Cuaca normal 🌤️';
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
            'weather_label' => $weatherLabel,
            'suggestion' => $suggestion,
            'bmkg_realtime' => [
                'description' => $bmkgWeather['description'],
                'temperature' => $bmkgWeather['temperature'] ? "{$bmkgWeather['temperature']}°C" : '-',
                'humidity' => $bmkgWeather['humidity'] ? "{$bmkgWeather['humidity']}%" : '-',
                'location' => $bmkgWeather['location'],
                'source' => $bmkgWeather['source'],
            ],
            'recommended_items' => $data,
            'total_recommendations' => count($data),
        ];
    }

    // =========================================================================
    // BMKG REAL-TIME WEATHER
    // =========================================================================

    /**
     * Fetch current weather from BMKG public API.
     * Cached for 30 minutes to minimize API calls.
     */
    private function fetchCurrentWeatherFromBMKG(): array
    {
        return Cache::remember('bmkg_weather_current', 1800, function () {
            try {
                $response = Http::timeout(10)
                    ->get('https://api.bmkg.go.id/publik/prakiraan-cuaca', [
                        'adm4' => $this->bmkgAdm4Code,
                    ]);

                if (!$response->successful()) {
                    Log::warning('BMKG API returned non-200', ['status' => $response->status()]);
                    return $this->getFallbackWeather();
                }

                $json = $response->json();
                $forecasts = $json['data'][0]['cuaca'] ?? [];
                $location = $json['lokasi'] ?? [];

                // Flatten all forecast entries
                $allForecasts = [];
                foreach ($forecasts as $group) {
                    foreach ($group as $entry) {
                        $allForecasts[] = $entry;
                    }
                }

                if (empty($allForecasts)) {
                    return $this->getFallbackWeather();
                }

                // Find the forecast closest to current time
                $now = now('Asia/Jakarta');
                $closest = null;
                $minDiff = PHP_INT_MAX;

                foreach ($allForecasts as $entry) {
                    $forecastTime = \Carbon\Carbon::parse($entry['local_datetime'], 'Asia/Jakarta');
                    $diff = abs($now->diffInMinutes($forecastTime));
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $closest = $entry;
                    }
                }

                if (!$closest) {
                    return $this->getFallbackWeather();
                }

                // Map BMKG weather code to our internal condition
                $weatherDesc = $closest['weather_desc'] ?? 'Tidak diketahui';
                $temperature = $closest['t'] ?? null;
                $humidity = $closest['hu'] ?? null;
                $condition = $this->mapBmkgToCondition($weatherDesc, $temperature);

                return [
                    'condition' => $condition,
                    'description' => $weatherDesc,
                    'temperature' => $temperature,
                    'humidity' => $humidity,
                    'location' => ($location['kecamatan'] ?? 'Cikarang Utara') . ', ' . ($location['kotkab'] ?? 'Bekasi'),
                    'forecast_time' => $closest['local_datetime'] ?? now()->toDateTimeString(),
                    'source' => 'BMKG',
                ];

            } catch (\Exception $e) {
                Log::error('BMKG API fetch error: ' . $e->getMessage());
                return $this->getFallbackWeather();
            }
        });
    }

    /**
     * Map BMKG weather description + temperature to our internal condition.
     */
    private function mapBmkgToCondition(string $weatherDesc, ?int $temperature): string
    {
        $desc = strtolower($weatherDesc);

        // Rain conditions
        if (str_contains($desc, 'hujan') || str_contains($desc, 'petir') || str_contains($desc, 'rain') || str_contains($desc, 'thunder')) {
            return 'rainy';
        }

        // Hot conditions (temperature based)
        if ($temperature !== null && $temperature >= 32) {
            return 'hot';
        }

        // Cool/cold conditions
        if ($temperature !== null && $temperature <= 23) {
            return 'cold';
        }

        // Cloudy
        if (str_contains($desc, 'berawan') || str_contains($desc, 'mendung') || str_contains($desc, 'cloudy')) {
            return 'normal';
        }

        // Sunny/clear and warm
        if ((str_contains($desc, 'cerah') || str_contains($desc, 'sunny') || str_contains($desc, 'clear')) && $temperature >= 30) {
            return 'hot';
        }

        return 'normal';
    }

    private function getFallbackWeather(): array
    {
        return [
            'condition' => 'normal',
            'description' => 'Data tidak tersedia',
            'temperature' => null,
            'humidity' => null,
            'location' => 'Cikarang Utara, Bekasi',
            'forecast_time' => now()->toDateTimeString(),
            'source' => 'fallback',
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
