<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GetBSSQuestionById;
use Tests\TestCase;

class GetBSSQuestionByIdTest extends TestCase
{
    public function test_returns_question_by_id(): void
    {
        $action = new GetBSSQuestionById;

        $result = $action->handle(1);

        $this->assertNotNull($result);
        $this->assertSame(1, $result['id']);
        $this->assertArrayHasKey('question', $result);
        $this->assertArrayHasKey('answer', $result);
    }

    public function test_returns_null_for_nonexistent_id(): void
    {
        $action = new GetBSSQuestionById;

        $result = $action->handle(9999);

        $this->assertNull($result);
    }
}
