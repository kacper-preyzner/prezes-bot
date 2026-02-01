<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

class EveryWeekAt implements Interval
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

    /** @param  int  $day  0=Sunday..6=Saturday */
    public function __construct(
        public readonly int $day,
        public readonly string $time,
    ) {}

    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable
    {
        $candidate = $current->next($this->day)->setTimeFromTimeString($this->time);

        // If the target day is today and the time hasn't passed yet, use today
        if ($current->dayOfWeek === $this->day) {
            $todayCandidate = $current->setTimeFromTimeString($this->time);
            if ($todayCandidate->greaterThan($current)) {
                return $todayCandidate;
            }
        }

        return $candidate;
    }

    public function label(): string
    {
        $dayName = self::DAY_NAMES[$this->day] ?? (string) $this->day;

        return "Co tydzień w {$dayName} o {$this->time}";
    }

    public function toArray(): array
    {
        return ['type' => 'every_week_at', 'day' => $this->day, 'time' => $this->time];
    }
}
