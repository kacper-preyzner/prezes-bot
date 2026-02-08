<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GenerateTTS;
use Prism\Prism\Audio\AudioResponse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\GeneratedAudio;
use Tests\TestCase;

class GenerateTTSTest extends TestCase
{
    public function test_returns_decoded_audio_bytes(): void
    {
        Prism::fake([
            new AudioResponse(
                audio: new GeneratedAudio(base64_encode('fake-audio-data')),
            ),
        ]);

        $action = new GenerateTTS;
        $result = $action->handle('Hello');

        $this->assertSame('fake-audio-data', $result);
    }
}
