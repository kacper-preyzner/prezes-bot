<?php

declare(strict_types=1);

namespace App\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Tool;

class GetTools
{
    public function __construct(
        protected CreatePlannedTask $createPlannedTask,
        protected WebSearch $webSearch,
    ) {}

    public function handle()
    {
        $createPlannedTaskTool = Tool::as('create_planned_task')
            ->for('Plan task to execute later')
            ->withStringParameter('instruction', 'Instruction for the AI that will handle this task later, e.g. "Przypomnij użytkownikowi żeby umył zęby". Should be a directive for the AI, not a direct task description.')
            ->withStringParameter('executeAt', 'Timestamp of when to execute the task')
            ->withBooleanParameter('repeating', 'Wheather this is one-time task or not')
            ->using(function (string $instruction, string $executeAt, bool $repeating): string {
                Log::debug('create_planned_task called', compact('instruction', 'executeAt', 'repeating'));
                $task = $this->createPlannedTask->handle(
                    $instruction,
                    CarbonImmutable::parse($executeAt, 'Europe/Warsaw'),
                    $repeating,
                );

                return "Task created: {$task->instruction} scheduled at {$task->execute_at}";
            });

        $webSearchTool = Tool::as('web_search')
            ->for('Search the web for current information, news, trends, etc. Returns AI-synthesized answer with citations.')
            ->withStringParameter('query', 'The search query')
            ->using(function (string $query): string {
                Log::debug('web_search called', ['query' => $query]);

                return $this->webSearch->handle($query);
            });

        return [$createPlannedTaskTool, $webSearchTool];
    }
}
