<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ExecuteTaskWithAI;
use App\Models\PlannedTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecutePlannedTasks extends Command
{
    protected $signature = 'tasks:execute';

    protected $description = 'Execute planned tasks that are due';

    public function handle(ExecuteTaskWithAI $executor): void
    {
        $tasks = PlannedTask::where('execute_at', '<=', now())
            ->where('is_running', false)
            ->get();

        foreach ($tasks as $task) {
            $claimed = PlannedTask::where('id', $task->id)
                ->where('is_running', false)
                ->update(['is_running' => true]);

            if ($claimed === 0) {
                Log::debug("Task #{$task->id}: already claimed by another process, skipping");

                continue;
            }

            Log::debug("Task #{$task->id}: claimed, starting execution", [
                'instruction' => $task->instruction,
                'execute_at' => $task->execute_at,
            ]);

            try {
                $executor->handle($task);
                Log::debug("Task #{$task->id}: execution finished successfully");
            } catch (\Throwable $e) {
                Log::error("Task #{$task->id}: execution failed", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            } finally {
                $task->refresh();

                $interval = $task->intervalObject();

                if ($interval !== null) {
                    $nextExecuteAt = $interval->nextExecuteAt($task->execute_at);
                    $task->update([
                        'execute_at' => $nextExecuteAt,
                        'is_running' => false,
                    ]);
                    Log::debug("Task #{$task->id}: rescheduled to {$nextExecuteAt}");
                } else {
                    $task->delete();
                    Log::debug("Task #{$task->id}: one-time task, deleted");
                }
            }
        }
    }
}
