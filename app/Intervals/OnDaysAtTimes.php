<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

class OnDaysAtTimes implements Interval
{
    private const array DAY_NAMES = [
        0 => 'niedziela',
        1 => 'poniedziałek',
        2 => 'wtorek',
        3 => 'środa',
        4 => 'czwartek',
        5 => 'piątek',
        6 => 'sobota',
    ];

    /** @param  array<int, list<string>>  $schedule  e.g. {4: ["14:00", "20:00"], 5: ["16:00"]} */
    public function __construct(
        public readonly array $schedule,
    ) {}

    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable
    {
        // Check up to 7 days ahead (covers full week cycle)
        for ($offset = 0; $offset <= 7; $offset++) {
            $day = $current->addDays($offset);
            $dayOfWeek = $day->dayOfWeek;

            if (! isset($this->schedule[$dayOfWeek])) {
                continue;
            }

            $times = $this->schedule[$dayOfWeek];
            sort($times);

            foreach ($times as $time) {
                $candidate = $day->setTimeFromTimeString($time);
                if ($candidate->greaterThan($current)) {
                    return $candidate;
                }
            }
        }

        // Fallback: first time of first day in schedule next week
        $days = array_keys($this->schedule);
        sort($days);
        $firstDay = $days[0];
        $times = $this->schedule[$firstDay];
        sort($times);

        return $current->next($firstDay)->setTimeFromTimeString($times[0]);
    }

    public function label(): string
    {
        $parts = [];

        $days = array_keys($this->schedule);
        sort($days);

        foreach ($days as $day) {
            $dayName = self::DAY_NAMES[$day] ?? (string) $day;
            $times = $this->schedule[$day];
            sort($times);
            $parts[] = $dayName.' o '.implode(', ', $times);
        }

        return implode('; ', $parts);
    }

    public function toArray(): array
    {
        return ['type' => 'on_days_at_times', 'schedule' => $this->schedule];
    }
}
