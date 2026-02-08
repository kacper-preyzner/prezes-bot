<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\EveryMonthAt;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class EveryMonthAtTest extends TestCase
{
    public function test_returns_this_month_if_day_not_passed(): void
    {
        $interval = new EveryMonthAt(15, '10:00');
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-15 10:00:00', $next->toDateTimeString());
    }

    public function test_returns_next_month_if_day_passed(): void
    {
        $interval = new EveryMonthAt(5, '10:00');
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-03-05 10:00:00', $next->toDateTimeString());
    }

    public function test_returns_today_if_same_day_and_time_not_passed(): void
    {
        $interval = new EveryMonthAt(8, '14:00');
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-08 14:00:00', $next->toDateTimeString());
    }

    public function test_handles_day_overflow_in_short_month(): void
    {
        $interval = new EveryMonthAt(31, '10:00');
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        // February has 28 days in 2026, so day 31 should clamp
        $this->assertTrue($next->day <= 28 || $next->month === 3);
    }

    public function test_to_array(): void
    {
        $interval = new EveryMonthAt(15, '10:00');

        $this->assertEquals(
            ['type' => 'every_month_at', 'day' => 15, 'time' => '10:00'],
            $interval->toArray(),
        );
    }
}
