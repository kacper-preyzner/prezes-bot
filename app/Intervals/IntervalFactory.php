<?php

declare(strict_types=1);

namespace App\Intervals;

use InvalidArgumentException;

class IntervalFactory
{
    /** @param  array<string, mixed>  $data */
    public static function fromArray(array $data): Interval
    {
        $data = self::normalize($data);

        return match ($data['type']) {
            'every_n_seconds' => new EveryNSeconds($data['n']),
            'every_n_minutes' => new EveryNMinutes($data['n']),
            'at_times_of_day' => new AtTimesOfDay($data['times']),
            'every_week_at' => new EveryWeekAt($data['day'], $data['time']),
            'every_month_at' => new EveryMonthAt($data['day'], $data['time']),
            'on_days_at_times' => new OnDaysAtTimes(self::normalizeSchedule($data['schedule'])),
            default => throw new InvalidArgumentException("Unknown interval type: {$data['type']}"),
        };
    }

    /**
     * Normalize AI responses that may omit the "type" key,
     * e.g. {"on_days_at_times": {"1": ["08:00"]}} instead of {"type": "on_days_at_times", "schedule": {"1": ["08:00"]}}
     */
    private static function normalize(array $data): array
    {
        if (isset($data['type'])) {
            return $data;
        }

        $knownTypes = ['every_n_seconds', 'every_n_minutes', 'at_times_of_day', 'every_week_at', 'every_month_at', 'on_days_at_times'];

        foreach ($knownTypes as $type) {
            if (isset($data[$type])) {
                $value = $data[$type];

                return match ($type) {
                    'on_days_at_times' => ['type' => 'on_days_at_times', 'schedule' => $value],
                    default => ['type' => $type, ...(is_array($value) ? $value : [])],
                };
            }
        }

        throw new InvalidArgumentException('Cannot determine interval type from: '.json_encode($data));
    }

    /**
     * Ensure schedule keys are integers.
     *
     * @return array<int, list<string>>
     */
    private static function normalizeSchedule(array $schedule): array
    {
        $normalized = [];

        foreach ($schedule as $day => $times) {
            $normalized[(int) $day] = is_array($times) ? $times : [(string) $times];
        }

        return $normalized;
    }
}
