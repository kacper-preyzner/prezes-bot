<?php

declare(strict_types=1);

namespace App\Skills;

interface Skill
{
    public function systemPrompt(): string;

    /** @return array<int, \Prism\Prism\Tool\ProviderTool> */
    public function tools(): array;
}
