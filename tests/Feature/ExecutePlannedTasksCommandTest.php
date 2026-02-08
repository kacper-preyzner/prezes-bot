<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PlannedTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;
use Tests\TestCase;

class ExecutePlannedTasksCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_executes_due_one_time_task_and_deletes_it(): void
    {
        Prism::fake([
            TextResponseFake::make()
                ->withText('Task executed')
                ->withUsage(new Usage(10, 20)),
        ]);

        $task = PlannedTask::create([
            'instruction' => 'Send a greeting',
            'execute_at' => now()->subMinute(),
            'interval' => null,
            'is_running' => false,
        ]);

        $this->artisan('tasks:execute')->assertSuccessful();

        $this->assertDatabaseMissing('planned_tasks', ['id' => $task->id]);
    }

    public function test_skips_future_tasks(): void
    {
        $task = PlannedTask::create([
            'instruction' => 'Future task',
            'execute_at' => now()->addHour(),
            'interval' => null,
            'is_running' => false,
        ]);

        $this->artisan('tasks:execute')->assertSuccessful();

        $this->assertDatabaseHas('planned_tasks', ['id' => $task->id]);
    }

    public function test_reschedules_recurring_task(): void
    {
        Prism::fake([
            TextResponseFake::make()
                ->withText('Task executed')
                ->withUsage(new Usage(10, 20)),
        ]);

        $task = PlannedTask::create([
            'instruction' => 'Recurring task',
            'execute_at' => now()->subMinute(),
            'interval' => ['type' => 'every_n_minutes', 'n' => 5],
            'is_running' => false,
        ]);

        $this->artisan('tasks:execute')->assertSuccessful();

        $task->refresh();

        $this->assertDatabaseHas('planned_tasks', ['id' => $task->id]);
        $this->assertFalse($task->is_running);
        $this->assertTrue($task->execute_at->isFuture());
    }

    public function test_skips_already_running_tasks(): void
    {
        $task = PlannedTask::create([
            'instruction' => 'Running task',
            'execute_at' => now()->subMinute(),
            'interval' => null,
            'is_running' => true,
        ]);

        $this->artisan('tasks:execute')->assertSuccessful();

        $task->refresh();

        $this->assertDatabaseHas('planned_tasks', ['id' => $task->id]);
        $this->assertTrue($task->is_running);
    }
}
