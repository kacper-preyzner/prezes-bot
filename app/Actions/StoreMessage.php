<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Message;

class StoreMessage
{
    public function handle(string $role, string $content): Message
    {
        return Message::create(['role' => $role, 'content' => trim($content)]);
    }
}
