<?php

declare(strict_types=1);

namespace App\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                Log::debug('create_planned_task called', compact('instruction', 'executeAt', 'repeating'));
                $task = $this->createPlannedTask->handle(
                    $instruction,
                    CarbonImmutable::parse($executeAt, 'Europe/Warsaw'),
                    $repeating,
                );

                return "Task created: {$task->instruction} scheduled at {$task->execute_at}";
            });

        $getCurrentTimeTool = Tool::as('get_current_time')
            ->for('Get the current date and time')
            ->using(function (): string {
                $now = CarbonImmutable::now('Europe/Warsaw')->toDateTimeString();
                Log::debug('get_current_time called', ['time' => $now]);

                return $now;
            });

        $webSearchTool = Tool::as('web_search')
            ->for('Search the web for current information, news, trends, etc.')
            ->withStringParameter('query', 'The search query')
            ->using(function (string $query): string {
                Log::debug('web_search called', ['query' => $query]);
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'X-Subscription-Token' => config('services.brave.api_key'),
                ])->get('https://api.search.brave.com/res/v1/web/search', [
                    'q' => $query,
                    'count' => 5,
                ]);

                $results = $response->json('web.results', []);

                return collect($results)->map(fn (array $result) => implode("\n", [
                    "Title: {$result['title']}",
                    "URL: {$result['url']}",
                    "Description: {$result['description']}",
                ]))->implode("\n\n");
            });

        return [$createPlannedTaskTool, $getCurrentTimeTool, $webSearchTool];
    }
}
