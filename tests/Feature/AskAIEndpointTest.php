<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class AskAIEndpointTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_requires_authentication(): void
    {
        $response = $this->postJson('/api/ask', ['prompt' => 'Hello']);

        $response->assertStatus(403);
    }

    public function test_validates_prompt_is_required(): void
    {
        $this->createAuthToken();

        $response = $this->postJson('/api/ask', [], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_returns_ai_response(): void
    {
        $this->createAuthToken();

        Prism::fake([
            TextResponseFake::make()
                ->withText('Fake AI response')
                ->withUsage(new Usage(10, 20)),
        ]);

        $response = $this->postJson('/api/ask', [
            'prompt' => 'Hello AI',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user_message',
                'assistant_message',
                'actions',
            ]);
    }

    public function test_stores_messages_in_database(): void
    {
        $this->createAuthToken();

        Prism::fake([
            TextResponseFake::make()
                ->withText('Fake AI response')
                ->withUsage(new Usage(10, 20)),
        ]);

        $this->postJson('/api/ask', [
            'prompt' => 'Hello AI',
        ], $this->authHeaders());

        $this->assertDatabaseCount('messages', 2);
        $this->assertDatabaseHas('messages', ['role' => 'user', 'content' => 'Hello AI']);
        $this->assertDatabaseHas('messages', ['role' => 'assistant', 'content' => 'Fake AI response']);
    }
}
