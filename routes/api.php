<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransWebhookController;
use App\Http\Controllers\Api\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);

// Chatbot AI Routes
Route::prefix('v1/chatbot')->group(function () {
    Route::post('/message', [ChatbotController::class, 'message']);
    Route::get('/menu', [ChatbotController::class, 'menu']);
    Route::get('/health', [ChatbotController::class, 'health']);
});
