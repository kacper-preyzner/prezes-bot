<?php

declare(strict_types=1);

namespace Tests\Unit\Intervals;

use App\Intervals\AtTimesOfDay;
use App\Intervals\EveryMonthAt;
use App\Intervals\EveryNMinutes;
use App\Intervals\EveryNSeconds;
use App\Intervals\EveryWeekAt;
use App\Intervals\IntervalFactory;
use App\Intervals\OnDaysAtTimes;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IntervalFactoryTest extends TestCase
{
    public function test_creates_every_n_seconds(): void
    {
        $interval = IntervalFactory::fromArray(['type' => 'every_n_seconds', 'n' => 30]);

        $this->assertInstanceOf(EveryNSeconds::class, $interval);
    }

    public function test_creates_every_n_minutes(): void
    {
        $interval = IntervalFactory::fromArray(['type' => 'every_n_minutes', 'n' => 5]);

        $this->assertInstanceOf(EveryNMinutes::class, $interval);
    }

    public function test_creates_at_times_of_day(): void
    {
        $interval = IntervalFactory::fromArray(['type' => 'at_times_of_day', 'times' => ['08:00', '20:00']]);

        $this->assertInstanceOf(AtTimesOfDay::class, $interval);
    }

    public function test_creates_every_week_at(): void
    {
        $interval = IntervalFactory::fromArray(['type' => 'every_week_at', 'day' => 1, 'time' => '09:00']);

        $this->assertInstanceOf(EveryWeekAt::class, $interval);
    }

    public function test_creates_every_month_at(): void
    {
        $interval = IntervalFactory::fromArray(['type' => 'every_month_at', 'day' => 15, 'time' => '10:00']);

        $this->assertInstanceOf(EveryMonthAt::class, $interval);
    }

    public function test_creates_on_days_at_times(): void
    {
        $interval = IntervalFactory::fromArray([
            'type' => 'on_days_at_times',
            'schedule' => ['4' => ['14:00', '20:00']],
        ]);

        $this->assertInstanceOf(OnDaysAtTimes::class, $interval);
    }

    public function test_normalizes_ai_response_without_type_key(): void
    {
        $interval = IntervalFactory::fromArray([
            'on_days_at_times' => ['1' => ['08:00']],
        ]);

        $this->assertInstanceOf(OnDaysAtTimes::class, $interval);
    }

    public function test_normalizes_schedule_keys_to_integers(): void
    {
        $interval = IntervalFactory::fromArray([
            'type' => 'on_days_at_times',
            'schedule' => ['4' => ['14:00'], '5' => ['16:00']],
        ]);

        $this->assertInstanceOf(OnDaysAtTimes::class, $interval);
        $this->assertArrayHasKey(4, $interval->schedule);
        $this->assertArrayHasKey(5, $interval->schedule);
    }

    public function test_throws_on_unknown_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IntervalFactory::fromArray(['type' => 'unknown_type']);
    }

    public function test_throws_when_type_cannot_be_determined(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IntervalFactory::fromArray(['foo' => 'bar']);
    }
}
