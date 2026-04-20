<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Chatbot\OllamaChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    private OllamaChatService $chatService;

    public function __construct(OllamaChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * POST /api/v1/chatbot/message
     */
    public function message(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'sometimes|array',
            'role' => 'sometimes|string|in:customer,cashier'
        ]);

        // Ollama can be slow on first load, extend PHP execution time
        set_time_limit(120);

        $message = $request->input('message');
        $history = $request->input('conversation_history', []);
        $role = $request->input('role', 'customer');

        $result = $this->chatService->chat($message, $history, $role);

        return response()->json([
            'success' => $result['success'],
            'data' => [
                'message' => $result['message'],
                'function_called' => $result['function_called'] ?? null,
            ],
        ], $result['success'] ? 200 : 503);
    }

    /**
     * GET /api/v1/chatbot/menu
     */
    public function menu(): JsonResponse
    {
        try {
            $categories = Category::with(['products' => function ($query) {
                $query->where('is_active', true)->orderBy('name');
            }])->get();

            $data = $categories->map(fn($cat) => [
                'category' => $cat->name,
                'items' => $cat->products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => 'Rp ' . number_format($p->base_price, 0, ',', '.'),
                    'price_raw' => $p->base_price,
                    'description' => $p->description,
                    'tags' => $p->tags,
                ]),
            ])->filter(fn($c) => $c['items']->isNotEmpty())->values();

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat menu.'], 500);
        }
    }

    /**
     * GET /api/v1/chatbot/health
     */
    public function health(): JsonResponse
    {
        $ollamaUrl = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $ollamaModel = env('OLLAMA_MODEL', 'llama3.1');

        // Quick ping to Ollama
        $ollamaOnline = false;
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get($ollamaUrl);
            $ollamaOnline = $response->successful();
        } catch (\Exception $e) {
            // Ollama not reachable
        }

        return response()->json([
            'success' => true,
            'message' => 'Keshir Chatbot API is running',
            'version' => '2.0.0',
            'ai_engine' => 'Ollama (Local)',
            'ollama_model' => $ollamaModel,
            'ollama_online' => $ollamaOnline,
        ]);
    }
}
