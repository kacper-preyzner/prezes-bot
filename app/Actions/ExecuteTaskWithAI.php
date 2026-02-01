<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\PlannedTask;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
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

        $nextExecuteAt = null;

        $executeTaskTool = Tool::as('execute_task')
            ->for('Send the task result to the user as a push notification and store it in chat history. Generate a short, catchy title and a message body.')
            ->withStringParameter(
                'title',
                'Short notification title, e.g. "Oto 5 trendów z dzisiaj specjalnie dla ciebie"',
            )
            ->withStringParameter('message', 'The notification body with the full message content')
            ->using(function (string $title, string $message): string {
                Log::debug('Executing task: sending notification and storing message...');
                $this->sendPushNotification->handle($title, $message);
                $this->storeMessage->handle('assistant', $message);

                return 'Task executed: notification sent and message stored.';
            });

        $rescheduleTaskTool = Tool::as('reschedule_task')->for(
            'Reschedule a repeating task to its next execution time. Only use this for repeating tasks.',
        )->withStringParameter(
            'next_execute_at',
            'The next execution datetime in Y-m-d H:i:s format (Europe/Warsaw timezone)',
        )->using(function (string $next_execute_at) use (&$nextExecuteAt): string {
            $nextExecuteAt = CarbonImmutable::parse($next_execute_at, 'Europe/Warsaw');
            Log::debug('reschedule_task called', ['next_execute_at' => $nextExecuteAt->toDateTimeString()]);

            return "Task rescheduled to {$nextExecuteAt->toDateTimeString()}";
        });

        $tools[] = $executeTaskTool;
        $tools[] = $rescheduleTaskTool;

        $now = CarbonImmutable::now('Europe/Warsaw')->toDateTimeString();
        $repeatingInfo = $task->repeating
            ? 'To zadanie jest CYKLICZNE. Po wykonaniu MUSISZ użyć narzędzia reschedule_task, aby zaplanować następne wykonanie. Określ następny termin na podstawie kontekstu instrukcji (np. codziennie = +1 dzień, co tydzień = +7 dni, zachowaj tę samą godzinę).'
            : 'To zadanie jest JEDNORAZOWE. Nie planuj następnego wykonania.';

        $systemPrompt = <<<PROMPT
        AKTUALNY CZAS: {$now} (Europe/Warsaw).

        Jesteś asystentką wykonującą zaplanowane zadania. Wykonaj poniższą instrukcję i wyślij wynik do użytkownika za pomocą narzędzia execute_task.

        ZASADY:
        - Wykonaj instrukcję najlepiej jak potrafisz.
        - ZAWSZE wyślij wynik do użytkownika za pomocą execute_task.
        - {$repeatingInfo}
        - Używaj narzędzi od razu bez pytania o pozwolenie.
        PROMPT;

        Prism::text()
            ->using(Provider::OpenRouter, 'google/gemini-2.5-flash')
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($task->instruction)
            ->withTools($tools)
            ->withMaxSteps(10)
            ->asText();

        if ($task->repeating && $nextExecuteAt !== null) {
            $task->update(['execute_at' => $nextExecuteAt]);
        } else {
            $task->delete();
        }
    }
}
