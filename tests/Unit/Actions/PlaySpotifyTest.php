<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\PlaySpotify;
use App\Models\SpotifyToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlaySpotifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_throws_when_spotify_not_connected(): void
    {
        $this->expectException(\RuntimeException::class);

        $action = app(PlaySpotify::class);
        $action->handle('test song');
    }

    public function test_plays_track_successfully(): void
    {
        SpotifyToken::create([
            'access_token' => 'test',
            'refresh_token' => 'test',
            'expires_at' => now()->addHour(),
        ]);

        Http::fake([
            'api.spotify.com/v1/search*' => Http::response(['tracks' => ['items' => [
                ['uri' => 'spotify:track:123', 'name' => 'Test Song', 'artists' => [['name' => 'Test Artist']]],
            ]]]),
            'api.spotify.com/v1/me/player/play*' => Http::response(null, 204),
        ]);

        $action = app(PlaySpotify::class);
        $result = $action->handle('test song');

        $this->assertArrayHasKey('track', $result);
        $this->assertArrayHasKey('artist', $result);
        $this->assertSame('Test Song', $result['track']);
        $this->assertSame('Test Artist', $result['artist']);
    }

    public function test_throws_when_no_tracks_found(): void
    {
        SpotifyToken::create([
            'access_token' => 'test',
            'refresh_token' => 'test',
            'expires_at' => now()->addHour(),
        ]);

        Http::fake([
            'api.spotify.com/v1/search*' => Http::response(['tracks' => ['items' => []]]),
        ]);

        $this->expectException(\RuntimeException::class);

        $action = app(PlaySpotify::class);
        $action->handle('nonexistent song');
    }
}
