<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\EveryNSeconds;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class EveryNSecondsTest extends TestCase
{
    public function test_calculates_next_execution(): void
    {
        $interval = new EveryNSeconds(30);
        $current = CarbonImmutable::parse('2026-02-08 10:00:00');

        $next = $interval->nextExecuteAt($current);

        $this->assertEquals('2026-02-08 10:00:30', $next->toDateTimeString());
    }

    public function test_to_array(): void
    {
        $interval = new EveryNSeconds(45);

        $this->assertEquals(['type' => 'every_n_seconds', 'n' => 45], $interval->toArray());
    }

    public function test_label(): void
    {
        $interval = new EveryNSeconds(60);

        $this->assertEquals('Co 60 sekund', $interval->label());
    }
}
