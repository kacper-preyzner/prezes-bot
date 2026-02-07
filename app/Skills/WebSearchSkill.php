<?php

declare(strict_types=1);

namespace App\Skills;

use App\Actions\WebSearch;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Tool;

class WebSearchSkill implements Skill
{
    public function __construct(
        protected WebSearch $webSearch,
    ) {}

    public function systemPrompt(): string
    {
        return '';
    }

    public function tools(): array
    {
        return [
            Tool::as('web_search')
                ->for(
                    'Search the web for current information, news, trends, etc. Returns AI-synthesized answer with citations.',
                )
                ->withStringParameter('query', 'The search query')
                ->using(function (string $query): string {
                    Log::debug('web_search called', ['query' => $query]);

                    return $this->webSearch->handle($query);
                }),
        ];
    }
}
