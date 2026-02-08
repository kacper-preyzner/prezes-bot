<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Prism\Prism\Audio\AudioResponse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\GeneratedAudio;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class TTSEndpointTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_requires_authentication(): void
    {
        $response = $this->postJson('/api/tts', ['text' => 'Hello']);

        $response->assertStatus(403);
    }

    public function test_validates_text_is_required(): void
    {
        $this->createAuthToken();

        $response = $this->postJson('/api/tts', [], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_returns_base64_audio(): void
    {
        $this->createAuthToken();

        Prism::fake([
            new AudioResponse(
                audio: new GeneratedAudio(
                    base64: base64_encode('fake-audio'),
                    type: 'audio/mpeg',
                ),
            ),
        ]);

        $response = $this->postJson('/api/tts', [
            'text' => 'Hello world',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['audio']);

        $audio = $response->json('audio');
        $this->assertNotEmpty($audio);
        $this->assertNotFalse(base64_decode($audio, true), 'Response audio should be a valid base64 string');
    }
}
