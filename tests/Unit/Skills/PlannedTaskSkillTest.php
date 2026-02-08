<?php

declare(strict_types=1);

namespace Tests\Unit\Skills;

use App\Models\PlannedTask;
use App\Skills\PlannedTaskSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlannedTaskSkillTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_planned_task_tool_creates_one_time_task(): void
    {
        $skill = app(PlannedTaskSkill::class);

        $tool = $this->findTool($skill->tools(), 'create_planned_task');
        $result = $tool->handle(
            instruction: 'Remind user to drink water',
            executeAt: '2026-03-01 10:00:00',
            interval: null,
        );

        $this->assertDatabaseCount('planned_tasks', 1);
        $this->assertDatabaseHas('planned_tasks', [
            'instruction' => 'Remind user to drink water',
        ]);
        $this->assertStringContainsString('Task created', $result);
    }

    public function test_create_planned_task_tool_creates_recurring_task(): void
    {
        $skill = app(PlannedTaskSkill::class);

        $tool = $this->findTool($skill->tools(), 'create_planned_task');
        $tool->handle(
            instruction: 'Daily reminder',
            executeAt: '2026-03-01 08:00:00',
            interval: '{"type":"at_times_of_day","times":["08:00","20:00"]}',
        );

        $task = PlannedTask::first();
        $this->assertNotNull($task);
        $this->assertNotNull($task->interval);
        $this->assertSame('at_times_of_day', $task->interval['type']);
    }

    public function test_create_planned_task_tool_handles_null_string_interval(): void
    {
        $skill = app(PlannedTaskSkill::class);

        $tool = $this->findTool($skill->tools(), 'create_planned_task');
        $tool->handle(
            instruction: 'One-time task',
            executeAt: '2026-03-01 10:00:00',
            interval: 'null',
        );

        $task = PlannedTask::first();
        $this->assertNotNull($task);
        $this->assertNull($task->interval);
    }

    public function test_system_prompt_is_not_empty(): void
    {
        $skill = app(PlannedTaskSkill::class);

        $this->assertNotEmpty($skill->systemPrompt());
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
