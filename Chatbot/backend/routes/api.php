<?php

use App\Http\Controllers\Api\ChatbotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Chatbot API Routes
|--------------------------------------------------------------------------
|
| Routes for the Keshir POS Chatbot module.
| Prefix: /api/v1/chatbot
|
| When migrating to main Keshir project, add this line to routes/api.php:
| require __DIR__.'/api_chatbot.php';
|
*/

Route::prefix('v1/chatbot')->group(function () {

    // === Public Routes (Customer Assistant) ===
    Route::post('/message', [ChatbotController::class, 'message']);
    Route::get('/menu', [ChatbotController::class, 'menu']);
    Route::get('/health', [ChatbotController::class, 'health']);

    // === Protected Routes (Cashier Assistant) ===
    // Uncomment below when integrating with main Keshir project:
    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::post('/cashier/message', [ChatbotController::class, 'cashierMessage']);
    // });

});
