<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SpotifyToken;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class SpotifyStatusEndpointTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_returns_not_connected_when_no_token(): void
    {
        $this->createAuthToken();

        $response = $this->getJson('/api/spotify/status', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson(['connected' => false]);
    }

    public function test_returns_connected_when_token_exists(): void
    {
        $this->createAuthToken();

        SpotifyToken::create([
            'access_token' => 'fake-access-token',
            'refresh_token' => 'fake-refresh-token',
            'expires_at' => CarbonImmutable::now()->addHour(),
        ]);

        $response = $this->getJson('/api/spotify/status', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson(['connected' => true]);
    }
}
