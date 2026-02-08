<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GetRandomBSSQuestion;
use Tests\TestCase;

class GetRandomBSSQuestionTest extends TestCase
{
    public function test_returns_question_with_id_and_text(): void
    {
        $action = new GetRandomBSSQuestion;

        $result = $action->handle();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('question', $result);
        $this->assertArrayNotHasKey('answer', $result);
    }

    public function test_returns_valid_question_id(): void
    {
        $action = new GetRandomBSSQuestion;

        $result = $action->handle();

        $this->assertGreaterThanOrEqual(1, $result['id']);
        $this->assertLessThanOrEqual(203, $result['id']);
    }
}
