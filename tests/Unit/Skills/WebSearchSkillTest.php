<?php

declare(strict_types=1);

namespace Tests\Unit\Skills;

use App\Skills\WebSearchSkill;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Tests\TestCase;

class WebSearchSkillTest extends TestCase
{
    public function test_web_search_tool_calls_prism_and_returns_result(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('Search results for weather'),
        ]);

        $skill = app(WebSearchSkill::class);
        $tool = $this->findTool($skill->tools(), 'web_search');

        $result = $tool->handle(query: 'weather in Warsaw');

        $this->assertSame('Search results for weather', $result);
    }

    public function test_system_prompt_is_empty(): void
    {
        $skill = app(WebSearchSkill::class);

        $this->assertSame('', $skill->systemPrompt());
    }

    public function test_registers_one_tool(): void
    {
        $skill = app(WebSearchSkill::class);

        $this->assertCount(1, $skill->tools());
        $this->assertSame('web_search', $skill->tools()[0]->name());
    }

    /**
     * @param  array<int, \Prism\Prism\Tool>  $tools
     */
    private function findTool(array $tools, string $name): \Prism\Prism\Tool
    {
        foreach ($tools as $tool) {
            if ($tool->name() === $name) {
                return $tool;
            }
        }

        $this->fail("Tool '{$name}' not found");
    }
}
