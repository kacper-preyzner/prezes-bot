<?php

declare(strict_types=1);

namespace App\Actions;

use Carbon\CarbonImmutable;
use Prism\Prism\Facades\Tool;

class GetTools
{
    public function __construct(
        protected CreatePlannedTask $createPlannedTask,
    ) {}

    public function handle()
    {
        $createPlannedTaskTool = Tool::as('create_planned_task')
            ->for('Plan task to execute later')
            ->withStringParameter('instruction', 'Instruction for the AI that will handle this task later, e.g. "Przypomnij użytkownikowi żeby umył zęby". Should be a directive for the AI, not a direct task description.')
            ->withStringParameter('executeAt', 'Timestamp of when to execute the task')
            ->withBooleanParameter('repeating', 'Wheather this is one-time task or not')
            ->using(function (string $instruction, string $executeAt, bool $repeating): string {
                $task = $this->createPlannedTask->handle(
                    $instruction,
                    CarbonImmutable::parse($executeAt, 'Europe/Warsaw'),
                    $repeating,
                );

                return "Task created: {$task->instruction} scheduled at {$task->execute_at}";
            });

        $getCurrentTimeTool = Tool::as('get_current_time')
            ->for('Get the current date and time')
            ->using(fn () => CarbonImmutable::now('Europe/Warsaw')->toDateTimeString());

        return [$createPlannedTaskTool, $getCurrentTimeTool];
    }
}
