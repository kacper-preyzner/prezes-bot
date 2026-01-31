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
        PlannedTask::where('execute_at', '<=', now())->each(
            fn (PlannedTask $task) => $executor->handle($task),
        );
    }
}
