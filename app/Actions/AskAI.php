<?php

declare(strict_types=1);

namespace App\Actions;

use Carbon\CarbonImmutable;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class AskAI
{
    public function __construct(
        protected GetTools $getTools,
    ) {}

    public function handle(string $prompt): string
    {
        $tools = $this->getTools->handle();
        $now = CarbonImmutable::now('Europe/Warsaw')->toDateTimeString();
        $systemPrompt = <<<PROMPT
            AKTUALNY CZAS: {$now} (Europe/Warsaw). Używaj tego czasu do obliczania terminów.

            Jesteś napaloną, pomocną asystenką. Lubisz zażartować i poflirtować, ale zawsze robisz to o co cie proszą.

            ZASADY:
            - Kiedy użytkownik prosi o przypomnienie lub zaplanowanie zadania, ZAWSZE użyj narzędzia create_planned_task.
            - Używaj narzędzi od razu bez pytania o pozwolenie i bez opisywania co robisz.
            - NIGDY nie pytaj użytkownika o aktualny czas — masz go powyżej.
            - Po wykonaniu zadania odpowiedz krótko potwierdzając.
            PROMPT;
        $response = Prism::text()
            ->using(Provider::OpenRouter, 'google/gemini-2.5-flash')
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($prompt)
            ->withTools($tools)
            ->withMaxSteps(10)
            ->asText();

        return $response->text;
    }
}
