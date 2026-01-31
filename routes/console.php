<?php

declare(strict_types=1);

use App\Actions\ExecuteTaskWithAI;
use App\Models\PlannedTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $executor = app(ExecuteTaskWithAI::class);

    PlannedTask::where('execute_at', '<=', now())->each(
        fn (PlannedTask $task) => $executor->handle($task),
    );
})->everyMinute();
