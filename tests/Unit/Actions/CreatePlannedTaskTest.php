<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreatePlannedTask;
use App\Intervals\EveryNMinutes;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePlannedTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_one_time_task(): void
    {
        $action = new CreatePlannedTask;

        $action->handle('Do something', CarbonImmutable::parse('2026-03-01 10:00:00'), null);

        $this->assertDatabaseHas('planned_tasks', [
            'instruction' => 'Do something',
            'execute_at' => '2026-03-01 10:00:00',
            'interval' => null,
        ]);
    }

    public function test_creates_recurring_task_with_interval(): void
    {
        $action = new CreatePlannedTask;

        $action->handle(
            'Do something recurring',
            CarbonImmutable::parse('2026-03-01 10:00:00'),
            new EveryNMinutes(5),
        );

        $this->assertDatabaseHas('planned_tasks', [
            'instruction' => 'Do something recurring',
            'interval' => json_encode(['type' => 'every_n_minutes', 'n' => 5]),
        ]);
    }
}
