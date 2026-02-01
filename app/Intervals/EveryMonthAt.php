<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

class EveryMonthAt implements Interval
{
    public function __construct(
        public readonly int $day,
        public readonly string $time,
    ) {}

    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable
    {
        // Try this month first
        if ($current->day < $this->day || ($current->day === $this->day && $current->setTimeFromTimeString($this->time)->greaterThan($current))) {
            $candidate = $current->day($this->day)->setTimeFromTimeString($this->time);
            if ($candidate->day === $this->day) {
                return $candidate;
            }
        }

        // Next month — handle day overflow (e.g. day 31 in a 30-day month)
        $nextMonth = $current->addMonth()->startOfMonth();
        $daysInMonth = $nextMonth->daysInMonth;
        $targetDay = min($this->day, $daysInMonth);

        return $nextMonth->day($targetDay)->setTimeFromTimeString($this->time);
    }

    public function label(): string
    {
        return "Co miesiąc {$this->day}. o {$this->time}";
    }

    public function toArray(): array
    {
        return ['type' => 'every_month_at', 'day' => $this->day, 'time' => $this->time];
    }
}
