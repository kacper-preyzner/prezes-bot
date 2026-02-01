<?php

declare(strict_types=1);

namespace App\Actions;

use App\Intervals\IntervalFactory;
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
            ->withStringParameter(
                'instruction',
                'Instruction for the AI that will handle this task later, e.g. "Przypomnij użytkownikowi żeby umył zęby". Should be a directive for the AI, not a direct task description.',
            )
            ->withStringParameter('executeAt', 'Timestamp of when to execute the task')
            ->withStringParameter(
                'interval',
                'Optional JSON string for repeating tasks. null for one-time tasks. Examples: {"type":"every_n_minutes","n":5}, {"type":"every_n_seconds","n":30}, {"type":"at_times_of_day","times":["08:00","20:00"]}, {"type":"every_week_at","day":1,"time":"09:00"} (day: 0=Sunday..6=Saturday), {"type":"every_month_at","day":15,"time":"10:00"}',
            )
            ->using(function (string $instruction, string $executeAt, ?string $interval = null): string {
                Log::debug('create_planned_task called', compact('instruction', 'executeAt', 'interval'));

                $parsedInterval = null;
                if ($interval !== null && $interval !== 'null') {
                    $parsedInterval = IntervalFactory::fromArray(json_decode($interval, true));
                }

                $task = $this->createPlannedTask->handle(
                    $instruction,
                    CarbonImmutable::parse($executeAt, 'Europe/Warsaw'),
                    $parsedInterval,
                );

                return "Task created: {$task->instruction} scheduled at {$task->execute_at}";
            });

        $webSearchTool = Tool::as('web_search')->for(
            'Search the web for current information, news, trends, etc. Returns AI-synthesized answer with citations.',
        )->withStringParameter('query', 'The search query')->using(function (string $query): string {
            Log::debug('web_search called', ['query' => $query]);

            return $this->webSearch->handle($query);
        });

        return [$createPlannedTaskTool, $webSearchTool];
    }
}
