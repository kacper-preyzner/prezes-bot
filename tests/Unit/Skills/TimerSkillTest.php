<?php

declare(strict_types=1);

namespace Tests\Unit\Skills;

use App\Data\ActionCollector;
use App\Data\ActionData;
use App\Skills\TimerSkill;
use Tests\TestCase;

class TimerSkillTest extends TestCase
{
    public function test_set_timer_tool_adds_action_to_collector(): void
    {
        $collector = new ActionCollector;
        $skill = new TimerSkill($collector);

        $tool = $this->findTool($skill->tools(), 'set_timer');
        $tool->handle(seconds: 120, message: 'Jajka');

        $actions = $collector->all();
        $this->assertCount(1, $actions);
        $this->assertInstanceOf(ActionData::class, $actions[0]);
        $this->assertSame('set_timer', $actions[0]->type);
        $this->assertSame(120, $actions[0]->seconds);
        $this->assertSame('Jajka', $actions[0]->message);
    }

    public function test_set_timer_tool_returns_confirmation_string(): void
    {
        $collector = new ActionCollector;
        $skill = new TimerSkill($collector);

        $tool = $this->findTool($skill->tools(), 'set_timer');
        $result = $tool->handle(seconds: 60, message: 'Przerwa');

        $this->assertStringContainsString('60', $result);
        $this->assertStringContainsString('Przerwa', $result);
    }

    public function test_system_prompt_is_not_empty(): void
    {
        $skill = new TimerSkill(new ActionCollector);

        $this->assertNotEmpty($skill->systemPrompt());
    }

    public function test_registers_one_tool(): void
    {
        $skill = new TimerSkill(new ActionCollector);

        $this->assertCount(1, $skill->tools());
        $this->assertSame('set_timer', $skill->tools()[0]->name());
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
