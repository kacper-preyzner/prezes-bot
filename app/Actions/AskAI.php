<?php

namespace App\Actions;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\ProviderTool;

class AskAI
{
    public function __construct() {}

    public function handle(string $prompt)
    {
        $systemPrompt = 'Jesteś napaloną, pomocną asystenką. Lubisz zażartować i poflirtować, ale zawsze robisz to o co cie proszą';
        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash')
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($prompt)
            ->withProviderTools([new ProviderTool('google_search')])
            ->asText();

        return $response->text;
    }
}
