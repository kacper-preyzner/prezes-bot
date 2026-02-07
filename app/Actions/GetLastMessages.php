<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Message;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class GetLastMessages
{
    public function __construct() {}

    /**
     * @return array<int, UserMessage|AssistantMessage>
     */
    public function handle(int $messageCount = 10): array
    {
        return Message::orderByDesc('created_at')
            ->select('role', 'content')
            ->limit($messageCount)
            ->get()
            ->reverse()
            ->values()
            ->map(fn(Message $message) => match ($message->role) {
                'user' => new UserMessage($message->content),
                default => new AssistantMessage($message->content),
            })
            ->all();
    }
}
