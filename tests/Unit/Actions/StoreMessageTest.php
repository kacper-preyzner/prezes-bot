<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\StoreMessage;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_stores_user_message(): void
    {
        $action = new StoreMessage;

        $action->handle('user', 'Hello');

        $this->assertDatabaseHas('messages', [
            'role' => 'user',
            'content' => 'Hello',
        ]);
    }

    public function test_stores_assistant_message(): void
    {
        $action = new StoreMessage;

        $action->handle('assistant', 'Hi there');

        $this->assertDatabaseHas('messages', [
            'role' => 'assistant',
            'content' => 'Hi there',
        ]);
    }

    public function test_trims_whitespace(): void
    {
        $action = new StoreMessage;

        $action->handle('user', '  hello  ');

        $this->assertDatabaseHas('messages', [
            'role' => 'user',
            'content' => 'hello',
        ]);
    }
}
