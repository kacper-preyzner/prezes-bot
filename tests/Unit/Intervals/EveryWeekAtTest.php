<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\EveryWeekAt;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class EveryWeekAtTest extends TestCase
{
    public function test_returns_next_week_same_day(): void
    {
        // 2026-02-08 is a Sunday (dayOfWeek=0), target Monday (1)
        $interval = new EveryWeekAt(1, '09:00');
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-09 09:00:00', $next->toDateTimeString());
    }

    public function test_returns_today_if_same_day_and_time_not_passed(): void
    {
        // 2026-02-09 is Monday (dayOfWeek=1)
        $interval = new EveryWeekAt(1, '14:00');
        $current = CarbonImmutable::parse('2026-02-09 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-09 14:00:00', $next->toDateTimeString());
    }

    public function test_returns_next_week_if_same_day_but_time_passed(): void
    {
        // 2026-02-09 is Monday (dayOfWeek=1)
        $interval = new EveryWeekAt(1, '09:00');
        $current = CarbonImmutable::parse('2026-02-09 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-16 09:00:00', $next->toDateTimeString());
    }

    public function test_to_array(): void
    {
        $interval = new EveryWeekAt(3, '15:00');

        $this->assertEquals(
            ['type' => 'every_week_at', 'day' => 3, 'time' => '15:00'],
            $interval->toArray(),
        );
    }
}
