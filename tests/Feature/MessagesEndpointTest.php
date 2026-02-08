<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithAuthToken;

class MessagesEndpointTest extends TestCase
{
    use RefreshDatabase;
    use WithAuthToken;

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/messages');

        $response->assertStatus(403);
    }

    public function test_returns_messages_in_descending_order(): void
    {
        $this->createAuthToken();

        Message::create(['role' => 'user', 'content' => 'test 1']);
        Message::create(['role' => 'user', 'content' => 'test 2']);
        Message::create(['role' => 'user', 'content' => 'test 3']);

        $response = $this->getJson('/api/messages', $this->authHeaders());

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('test 3', $data[0]['content']);
        $this->assertEquals('test 2', $data[1]['content']);
        $this->assertEquals('test 1', $data[2]['content']);
    }

    public function test_limits_to_20_messages(): void
    {
        $this->createAuthToken();

        for ($i = 1; $i <= 25; $i++) {
            Message::create(['role' => 'user', 'content' => "test $i"]);
        }

        $response = $this->getJson('/api/messages', $this->authHeaders());

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(20, $data);
        $this->assertNotNull($response->json('next_cursor'));
    }

    public function test_supports_cursor_pagination(): void
    {
        $this->createAuthToken();

        for ($i = 1; $i <= 25; $i++) {
            Message::create(['role' => 'user', 'content' => "test $i"]);
        }

        $firstPage = $this->getJson('/api/messages', $this->authHeaders());
        $firstPage->assertStatus(200);

        $cursor = $firstPage->json('next_cursor');
        $this->assertNotNull($cursor);

        $secondPage = $this->getJson("/api/messages?cursor=$cursor", $this->authHeaders());
        $secondPage->assertStatus(200);

        $secondPageData = $secondPage->json('data');
        $this->assertCount(5, $secondPageData);
        $this->assertNull($secondPage->json('next_cursor'));
    }

    public function test_supports_after_parameter(): void
    {
        $this->createAuthToken();

        for ($i = 1; $i <= 5; $i++) {
            Message::create(['role' => 'user', 'content' => "test $i"]);
        }

        $secondMessage = Message::where('content', 'test 2')->first();

        $response = $this->getJson("/api/messages?after={$secondMessage->id}", $this->authHeaders());

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        foreach ($data as $message) {
            $this->assertGreaterThan($secondMessage->id, $message['id']);
        }
    }
}
