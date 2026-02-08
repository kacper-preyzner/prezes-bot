<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class CheckEndpointTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_returns_alive_status(): void
    {
        $this->createAuthToken();

        $response = $this->getJson('/api/check', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson(['status' => 'alive!']);
    }
}
