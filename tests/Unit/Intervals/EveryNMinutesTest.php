<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\EveryNMinutes;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class EveryNMinutesTest extends TestCase
{
    public function test_calculates_next_execution(): void
    {
        $interval = new EveryNMinutes(5);
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-08 10:05:00', $next->toDateTimeString());
    }

    public function test_to_array(): void
    {
        $interval = new EveryNMinutes(15);

        $this->assertEquals(['type' => 'every_n_minutes', 'n' => 15], $interval->toArray());
    }

    public function test_label(): void
    {
        $interval = new EveryNMinutes(10);

        $this->assertEquals('Co 10 minut', $interval->label());
    }
}
