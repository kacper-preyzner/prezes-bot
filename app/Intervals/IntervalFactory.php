<?php

declare(strict_types=1);

namespace App\Intervals;

use InvalidArgumentException;

class IntervalFactory
{
    /** @param  array<string, mixed>  $data */
    public static function fromArray(array $data): Interval
    {
        return match ($data['type']) {
            'every_n_seconds' => new EveryNSeconds($data['n']),
            'every_n_minutes' => new EveryNMinutes($data['n']),
            'at_times_of_day' => new AtTimesOfDay($data['times']),
            'every_week_at' => new EveryWeekAt($data['day'], $data['time']),
            'every_month_at' => new EveryMonthAt($data['day'], $data['time']),
            default => throw new InvalidArgumentException("Unknown interval type: {$data['type']}"),
        };
    }
}
