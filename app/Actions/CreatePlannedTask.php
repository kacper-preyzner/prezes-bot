<?php

declare(strict_types=1);

namespace App\Actions;

use App\Intervals\Interval;
use App\Models\PlannedTask;
use Carbon\CarbonImmutable;

class CreatePlannedTask
{
    public function handle(string $instruction, CarbonImmutable $executeAt, ?Interval $interval): PlannedTask
    {
        if ($interval !== null) {
            $now = CarbonImmutable::now('Europe/Warsaw');
            $executeAt = $interval->nextExecuteAt($now->subSecond());
        }

        return PlannedTask::create([
            'instruction' => $instruction,
            'execute_at' => $executeAt,
            'interval' => $interval?->toArray(),
        ]);
    }
}
