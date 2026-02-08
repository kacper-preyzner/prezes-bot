<?php

declare(strict_types=1);

namespace Tests\Unit\Skills;

use App\Skills\BssQuizSkill;
use Tests\TestCase;

class BssQuizSkillTest extends TestCase
{
    public function test_get_random_question_tool_returns_question(): void
    {
        $skill = app(BssQuizSkill::class);
        $tool = $this->findTool($skill->tools(), 'bss_quiz_get_random_question');

        $result = $tool->handle(count: 1);

        $this->assertStringContainsString('Question', $result);
        $this->assertMatchesRegularExpression('/Question \d+:/', $result);
    }

    public function test_check_answer_tool_returns_comparison(): void
    {
        $skill = app(BssQuizSkill::class);
        $tool = $this->findTool($skill->tools(), 'bss_quiz_check_answer');

        $result = $tool->handle(question_id: 1, user_answer: 'AH, ESP, IKE');

        $this->assertStringContainsString('User answered', $result);
        $this->assertStringContainsString('Correct answer', $result);
        $this->assertStringContainsString('AH, ESP, IKE', $result);
    }

    public function test_check_answer_tool_handles_nonexistent_question(): void
    {
        $skill = app(BssQuizSkill::class);
        $tool = $this->findTool($skill->tools(), 'bss_quiz_check_answer');

        $result = $tool->handle(question_id: 9999, user_answer: 'something');

        $this->assertStringContainsString('not found', $result);
    }

    public function test_get_question_by_id_tool_returns_question_with_answer(): void
    {
        $skill = app(BssQuizSkill::class);
        $tool = $this->findTool($skill->tools(), 'bss_quiz_get_question_by_id');

        $result = $tool->handle(question_id: 1);

        $this->assertStringContainsString('Question 1:', $result);
        $this->assertStringContainsString('Answer:', $result);
    }

    public function test_get_question_by_id_tool_handles_nonexistent(): void
    {
        $skill = app(BssQuizSkill::class);
        $tool = $this->findTool($skill->tools(), 'bss_quiz_get_question_by_id');

        $result = $tool->handle(question_id: 9999);

        $this->assertStringContainsString('not found', $result);
    }

    public function test_registers_three_tools(): void
    {
        $skill = app(BssQuizSkill::class);
        $tools = $skill->tools();

        $this->assertCount(3, $tools);

        $names = array_map(fn ($t) => $t->name(), $tools);
        $this->assertContains('bss_quiz_get_random_question', $names);
        $this->assertContains('bss_quiz_check_answer', $names);
        $this->assertContains('bss_quiz_get_question_by_id', $names);
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
