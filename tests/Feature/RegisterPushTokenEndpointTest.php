<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PushToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class RegisterPushTokenEndpointTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_requires_authentication(): void
    {
        $response = $this->postJson('/api/register-push-token', ['token' => 'ExponentPushToken[xxx]']);

        $response->assertStatus(403);
    }

    public function test_validates_token_is_required(): void
    {
        $this->createAuthToken();

        $response = $this->postJson('/api/register-push-token', [], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_registers_new_push_token(): void
    {
        $this->createAuthToken();

        $response = $this->postJson('/api/register-push-token', [
            'token' => 'ExponentPushToken[xxx]',
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseCount('push_tokens', 1);
        $this->assertDatabaseHas('push_tokens', ['token' => 'ExponentPushToken[xxx]']);
    }

    public function test_does_not_duplicate_existing_token(): void
    {
        $this->createAuthToken();

        $this->postJson('/api/register-push-token', [
            'token' => 'ExponentPushToken[xxx]',
        ], $this->authHeaders());

        $this->postJson('/api/register-push-token', [
            'token' => 'ExponentPushToken[xxx]',
        ], $this->authHeaders());

        $this->assertDatabaseCount('push_tokens', 1);
    }
}
