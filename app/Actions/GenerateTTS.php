<?php

declare(strict_types=1);

namespace App\Actions;

use Prism\Prism\Facades\Prism;

class GenerateTTS
{
    public function handle(string $text): string
    {
        $response = Prism::audio()
            ->using('elevenlabs', 'eleven_multilingual_v2')
            ->withInput($text)
            ->withVoice('piI8Kku0DcvcL6TTSeQt')
            ->withProviderOptions([
                'voice_settings' => [
                    'stability' => 0.3,
                    'similarity_boost' => 0.75,
                    'style' => 0.8,
                    'use_speaker_boost' => true,
                ],
            ])
            ->asAudio();

        return base64_decode($response->audio->base64);
    }
}
