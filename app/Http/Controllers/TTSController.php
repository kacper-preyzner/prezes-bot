<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GenerateTTS;
use App\Http\Requests\TTSRequest;
use Illuminate\Support\Facades\Log;

class TTSController extends Controller
{
    public function __invoke(TTSRequest $request, GenerateTTS $generateTTS)
    {
        $audio = $generateTTS->handle($request->validated('text'));

        Log::debug('TTS generated succesfully!');
        return response()->json([
            'audio' => base64_encode($audio),
        ]);
    }
}
