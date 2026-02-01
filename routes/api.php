<?php

declare(strict_types=1);

use App\Http\Controllers\AskAIController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\RegisterPushTokenController;
use App\Http\Controllers\TTSController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureTokenIsValid::class)->group(function () {
    Route::get('/check', function (Request $request) {
        return response()->json(['status' => 'alive!']);
    });

    Route::post('/ask', AskAIController::class);
    Route::post('/tts', TTSController::class);
    Route::post('/register-push-token', RegisterPushTokenController::class);
    Route::get('/messages', MessagesController::class);
});
