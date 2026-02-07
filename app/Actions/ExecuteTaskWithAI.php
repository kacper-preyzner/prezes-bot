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
        protected GetSettingsSet $getSettingsSet,
    ) {}

    public function handle(PlannedTask $task): void
    {
        $result = $this->getTools->handle();
        $tools = $result['tools'];

        $executeTaskTool = Tool::as('execute_task')
            ->for('Send the task result to the user as a push notification and store it in chat history. Generate a short, catchy title and a message body.')
            ->withStringParameter(
                'title',
                'Short notification title, e.g. "Oto 5 trendów z dzisiaj specjalnie dla ciebie"',
            )
            ->withStringParameter('message', 'The notification body with the full message content')
            ->using(function (string $title, string $message): string {
                Log::debug('execute_task tool called', ['title' => $title]);

                try {
                    $this->sendPushNotification->handle($title, $message);
                    Log::debug('execute_task: push notification sent');
                } catch (\Throwable $e) {
                    Log::error('execute_task: push notification failed', ['error' => $e->getMessage()]);
                }

                $this->storeMessage->handle('assistant', $message);
                Log::debug('execute_task: message stored');

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

        $settings = $this->getSettingsSet->handle();
        $model = $settings->open_router_llm_model;

        Log::debug("ExecuteTaskWithAI: calling Prism for task #{$task->id}", ['model' => $model]);

        $response = Prism::text()
            ->using(Provider::OpenRouter, $model)
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($task->instruction)
            ->withTools($tools)
            ->withMaxSteps(10)
            ->asText();

        Log::debug("ExecuteTaskWithAI: Prism finished for task #{$task->id}", [
            'steps' => $response->steps->count(),
            'finishReason' => $response->finishReason->name,
            'text' => mb_substr($response->text, 0, 200),
        ]);
    }
}
