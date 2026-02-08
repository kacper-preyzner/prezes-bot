<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\AtTimesOfDay;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class AtTimesOfDayTest extends TestCase
{
    public function test_returns_next_time_today(): void
    {
        $interval = new AtTimesOfDay(['08:00', '14:00', '20:00']);
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-08 14:00:00', $next->toDateTimeString());
    }

    public function test_wraps_to_next_day_when_all_times_passed(): void
    {
        $interval = new AtTimesOfDay(['08:00', '14:00', '20:00']);
        $current = CarbonImmutable::parse('2026-02-08 21:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-09 08:00:00', $next->toDateTimeString());
    }

    public function test_handles_unsorted_times(): void
    {
        $interval = new AtTimesOfDay(['20:00', '08:00', '14:00']);
        $current = CarbonImmutable::parse('2026-02-08 07:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-08 08:00:00', $next->toDateTimeString());
    }

    public function test_to_array(): void
    {
        $interval = new AtTimesOfDay(['08:00', '20:00']);

        $this->assertEquals(
            ['type' => 'at_times_of_day', 'times' => ['08:00', '20:00']],
            $interval->toArray(),
        );
    }

    public function test_label(): void
    {
        $interval = new AtTimesOfDay(['08:00', '20:00']);

        $this->assertEquals('Codziennie o 08:00, 20:00', $interval->label());
    }
}
