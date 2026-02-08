<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GetLastMessages;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Tests\TestCase;

class GetLastMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_empty_array_when_no_messages(): void
    {
        $action = new GetLastMessages;

        $result = $action->handle();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_returns_messages_as_prism_objects(): void
    {
        Message::create(['role' => 'user', 'content' => 'Hello', 'created_at' => now()->subMinute()]);
        Message::create(['role' => 'assistant', 'content' => 'Hi there', 'created_at' => now()]);

        $action = new GetLastMessages;
        $result = $action->handle();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(UserMessage::class, $result[0]);
        $this->assertInstanceOf(AssistantMessage::class, $result[1]);
    }

    public function test_returns_last_n_messages(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Message::create(['role' => 'user', 'content' => "Message {$i}"]);
        }

        $action = new GetLastMessages;
        $result = $action->handle(10);

        $this->assertCount(10, $result);
    }

    public function test_returns_messages_in_chronological_order(): void
    {
        Message::create(['role' => 'user', 'content' => 'Hello', 'created_at' => now()->subMinute()]);
        Message::create(['role' => 'assistant', 'content' => 'Hi there', 'created_at' => now()]);

        $action = new GetLastMessages;
        $result = $action->handle();

        $this->assertInstanceOf(UserMessage::class, $result[0]);
        $this->assertInstanceOf(AssistantMessage::class, $result[1]);
    }
}
