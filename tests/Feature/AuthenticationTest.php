<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_rejects_request_without_token(): void
    {
        $response = $this->getJson('/api/check');

        $response->assertStatus(403);
    }

    public function test_rejects_request_with_invalid_token(): void
    {
        $response = $this->getJson('/api/check', [
            'Authorization' => 'invalid-token',
        ]);

        $response->assertStatus(403);
    }

    public function test_accepts_request_with_valid_token(): void
    {
        $this->createAuthToken();

        $response = $this->getJson('/api/check', $this->authHeaders());

        $response->assertStatus(200);
    }
}
