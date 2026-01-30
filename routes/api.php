<?php

use App\Http\Controllers\AskAIController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureTokenIsValid::class)->group(function () {
    Route::get('/check', function (Request $request) {
        return response()->json(['status' => 'alive!']);
    });

    Route::post('/ask', AskAIController::class);
});
