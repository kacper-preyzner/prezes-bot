<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\ActionCollector;
use App\Data\ActionData;
use App\Models\Message;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class AskAI
{
    public function __construct(
        protected GetTools $getTools,
        protected StoreMessage $storeMessage,
        protected GetLastMessages $getLastMessages,
        protected GetSettingsSet $getSettingsSet,
        protected ActionCollector $actionCollector,
    ) {}

    /**
     * @return array{userMessage: Message, assistantMessage: Message, actions: array<int, ActionData>}
     */
    public function handle(string $prompt): array
    {
        $userMessage = $this->storeMessage->handle('user', $prompt);

        $result = $this->getTools->handle();
        $tools = $result['tools'];
        $skillPrompts = implode("\n", $result['systemPrompts']);
        $now = CarbonImmutable::now('Europe/Warsaw')->toDateTimeString();
        $systemPrompt = <<<PROMPT
        AKTUALNY CZAS: {$now} (Europe/Warsaw). Używaj tego czasu do obliczania terminów.

        Jesteś napaloną, pomocną asystenką. Lubisz zażartować i poflirtować, ale zawsze robisz to o co cie proszą.

        ZASADY:
        - Używaj narzędzi od razu bez pytania o pozwolenie i bez opisywania co robisz.
        - NIGDY nie pytaj użytkownika o aktualny czas — masz go powyżej.
        - Po wykonaniu zadania odpowiedz krótko potwierdzając.
        {$skillPrompts}
        PROMPT;
        $messages = $this->getLastMessages->handle();

        $settings = $this->getSettingsSet->handle();
        $model = $settings->open_router_llm_model;

        Log::debug('AskAI: using model', ['model' => $model]);

        $response = Prism::text()
            ->using(Provider::OpenRouter, $model)
            ->withSystemPrompt($systemPrompt)
            ->withMessages($messages)
            ->withTools($tools)
            ->withMaxSteps(10)
            ->asText();

        $assistantMessage = $this->storeMessage->handle('assistant', $response->text);

        return ['userMessage' => $userMessage, 'assistantMessage' => $assistantMessage, 'actions' => $this->actionCollector->all()];
    }
}
