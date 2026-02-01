<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlannedTasks\Pages;

use App\Filament\Resources\PlannedTasks\PlannedTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlannedTask extends CreateRecord
{
    protected static string $resource = PlannedTaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::buildInterval($data);
    }

    public static function buildInterval(array $data): array
    {
        $type = $data['interval_type'] ?? '';

        $data['interval'] = match ($type) {
            'every_n_seconds' => ['type' => 'every_n_seconds', 'n' => (int) $data['interval_n']],
            'every_n_minutes' => ['type' => 'every_n_minutes', 'n' => (int) $data['interval_n']],
            'at_times_of_day' => ['type' => 'at_times_of_day', 'times' => array_map('trim', explode(',', $data['interval_times']))],
            'every_week_at' => ['type' => 'every_week_at', 'day' => (int) $data['interval_day_of_week'], 'time' => $data['interval_time']],
            'every_month_at' => ['type' => 'every_month_at', 'day' => (int) $data['interval_day_of_month'], 'time' => $data['interval_time']],
            'on_days_at_times' => self::buildOnDaysAtTimes($data['interval_schedule'] ?? []),
            default => null,
        };

        unset($data['interval_type'], $data['interval_n'], $data['interval_times'], $data['interval_day_of_week'], $data['interval_day_of_month'], $data['interval_time'], $data['interval_schedule']);

        return $data;
    }

    private static function buildOnDaysAtTimes(array $items): array
    {
        $schedule = [];

        foreach ($items as $item) {
            $day = (int) $item['day'];
            $times = array_map('trim', explode(',', $item['times']));
            $schedule[$day] = $times;
        }

        return ['type' => 'on_days_at_times', 'schedule' => $schedule];
    }
}
