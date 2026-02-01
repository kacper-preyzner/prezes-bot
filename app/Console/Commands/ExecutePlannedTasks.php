<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ExecuteTaskWithAI;
use App\Models\PlannedTask;
use Illuminate\Console\Command;

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
                continue;
            }

            try {
                $executor->handle($task);
            } finally {
                $task->refresh();

                $interval = $task->intervalObject();

                if ($interval !== null) {
                    $task->update([
                        'execute_at' => $interval->nextExecuteAt($task->execute_at),
                        'is_running' => false,
                    ]);
                } else {
                    $task->delete();
                }
            }
        }
    }
}
