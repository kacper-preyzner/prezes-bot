<?php

declare(strict_types=1);

namespace App\Actions;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class WebSearch
{
    public function handle(string $query): string
    {
        $response = Prism::text()
            ->using(Provider::OpenRouter, 'perplexity/sonar')
            ->withPrompt($query)
            ->asText();

        return $response->text;
    }
}
