<?php

declare(strict_types=1);

namespace App\Skills;

use App\Data\ActionCollector;
use App\Data\ActionData;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Tool;

class TimerSkill implements Skill
{
    public function __construct(
        protected ActionCollector $actionCollector,
    ) {}

    public function systemPrompt(): string
    {
        return 'Kiedy użytkownik prosi o minutnik/timer/stoper, ZAWSZE użyj narzędzia set_timer.';
    }

    public function tools(): array
    {
        return [
            Tool::as('set_timer')
                ->for(
                    'Set a countdown timer on the user\'s device. Use when user asks to set a timer/minutnik/stoper. Use this ONLY IF USER TELLS YOU SPECIFICALLY TO USE timer/minutnik/stoper.',
                )
                ->withNumberParameter('seconds', 'Timer duration in seconds')
                ->withStringParameter('message', 'Short label for the timer, e.g. "Jajka", "Przerwa"')
                ->using(function (int $seconds, string $message): string {
                    Log::debug('set_timer called', compact('seconds', 'message'));

                    $this->actionCollector->add(new ActionData(
                        type: 'set_timer',
                        seconds: $seconds,
                        message: $message,
                    ));

                    return "Timer set for {$seconds} seconds with message: {$message}";
                }),
        ];
    }
}
