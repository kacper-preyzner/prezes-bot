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
        return PlannedTask::create([
            'instruction' => $instruction,
            'execute_at' => $executeAt,
            'interval' => $interval,
        ]);
    }
}
