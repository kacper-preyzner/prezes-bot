<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\PlannedTask;
use Carbon\CarbonImmutable;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Facades\Tool;

class ExecuteTaskWithAI
{
    public function __construct(
        protected GetTools $getTools,
        protected SendPushNotification $sendPushNotification,
        protected StoreMessage $storeMessage,
    ) {}

    public function handle(PlannedTask $task): void
    {
        $tools = $this->getTools->handle();

        $executeTaskTool = Tool::as('execute_task')
            ->for('Send the task result to the user as a push notification and store it in chat history. Generate a short, catchy title and a message body.')
            ->withStringParameter(
                'title',
                'Short notification title, e.g. "Oto 5 trendów z dzisiaj specjalnie dla ciebie"',
            )
            ->withStringParameter('message', 'The notification body with the full message content')
            ->using(function (string $title, string $message): string {
                $this->sendPushNotification->handle($title, $message);
                $this->storeMessage->handle('assistant', $message);

                return 'Task executed: notification sent and message stored.';
            });

        $tools[] = $executeTaskTool;

        $now = CarbonImmutable::now('Europe/Warsaw')->toDateTimeString();

        $systemPrompt = <<<PROMPT
        AKTUALNY CZAS: {$now} (Europe/Warsaw).

        Jesteś asystentką wykonującą zaplanowane zadania. Wykonaj poniższą instrukcję i wyślij wynik do użytkownika za pomocą narzędzia execute_task.

        ZASADY:
        - Wykonaj instrukcję najlepiej jak potrafisz.
        - ZAWSZE wyślij wynik do użytkownika za pomocą execute_task.
        - Używaj narzędzi od razu bez pytania o pozwolenie.
        PROMPT;

        Prism::text()
            ->using(Provider::OpenRouter, 'google/gemini-2.5-flash')
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($task->instruction)
            ->withTools($tools)
            ->withMaxSteps(10)
            ->asText();
    }
}
