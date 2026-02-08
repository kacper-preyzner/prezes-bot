<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\OnDaysAtTimes;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class OnDaysAtTimesTest extends TestCase
{
    public function test_returns_next_time_today(): void
    {
        // 2026-02-09 is Monday (dayOfWeek=1)
        $interval = new OnDaysAtTimes([
            1 => ['08:00', '14:00', '20:00'],
            5 => ['16:00'],
        ]);
        $current = CarbonImmutable::parse('2026-02-09 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-09 14:00:00', $next->toDateTimeString());
    }

    public function test_returns_next_scheduled_day(): void
    {
        // 2026-02-10 is Tuesday (dayOfWeek=2), next scheduled is Friday (5)
        $interval = new OnDaysAtTimes([
            1 => ['08:00'],
            5 => ['16:00'],
        ]);
        $current = CarbonImmutable::parse('2026-02-10 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-13 16:00:00', $next->toDateTimeString());
    }

    public function test_wraps_to_next_week(): void
    {
        // 2026-02-13 is Friday (dayOfWeek=5), after 16:00, next is Monday
        $interval = new OnDaysAtTimes([
            1 => ['08:00'],
            5 => ['16:00'],
        ]);
        $current = CarbonImmutable::parse('2026-02-13 17:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-16 08:00:00', $next->toDateTimeString());
    }

    public function test_to_array(): void
    {
        $schedule = [4 => ['14:00', '20:00'], 5 => ['16:00']];
        $interval = new OnDaysAtTimes($schedule);

        $this->assertEquals(
            ['type' => 'on_days_at_times', 'schedule' => $schedule],
            $interval->toArray(),
        );
    }
}
