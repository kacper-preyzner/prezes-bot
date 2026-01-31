<?php

namespace App\Actions;

use App\Models\PlannedTask;
use Carbon\CarbonImmutable;

class CreatePlannedTask
{
    public function __construct() {}

    public function handle(string $instruction, CarbonImmutable $executeAt, bool $repeating): PlannedTask
    {
        return PlannedTask::create([
            'instruction' => $instruction,
            'execute_at' => $executeAt,
            'repeating' => $repeating,
        ]);
    }
}
