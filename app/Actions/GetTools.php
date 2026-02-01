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
            ->for('Plan task to execute later. IMPORTANT: Always create exactly ONE task per user request. If the user wants something on multiple days/times, use on_days_at_times with a schedule covering all days and times — do NOT create separate tasks.')
            ->withStringParameter(
                'instruction',
                'Instruction for the AI that will handle this task later, e.g. "Przypomnij użytkownikowi żeby umył zęby". Should be a directive for the AI, not a direct task description.',
            )
            ->withStringParameter('executeAt', 'Timestamp of when to execute the task')
            ->withStringParameter(
                'interval',
                <<<'DESC'
                Optional JSON for repeating tasks. null = one-time. EVERY object MUST have a "type" key. Available types:
                - {"type":"every_n_seconds","n":30}
                - {"type":"every_n_minutes","n":5}
                - {"type":"at_times_of_day","times":["08:00","20:00"]} — same times every day
                - {"type":"every_week_at","day":1,"time":"09:00"} — single day+time weekly (day: 0=Sun,1=Mon,...,6=Sat)
                - {"type":"every_month_at","day":15,"time":"10:00"}
                - {"type":"on_days_at_times","schedule":{"4":["14:00","20:00"],"5":["16:00"]}} — PREFERRED for multiple days/times per week (keys: 0=Sun..6=Sat). Use this instead of creating multiple tasks!
                DESC,
            )
            ->using(function (string $instruction, string $executeAt, ?string $interval = null): string {
                Log::debug('create_planned_task called', compact('instruction', 'executeAt', 'interval'));

                $parsedInterval = null;
                if ($interval !== null && $interval !== 'null') {
                    $decoded = json_decode($interval, true);

                    if ($decoded === null) {
                        // AI sometimes sends single-quoted JSON
                        $decoded = json_decode(str_replace("'", '"', $interval), true);
                    }

                    if ($decoded !== null) {
                        $parsedInterval = IntervalFactory::fromArray($decoded);
                    }
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
