<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

class AtTimesOfDay implements Interval
{
    /** @param  list<string>  $times  e.g. ["08:00", "14:00", "20:00"] */
    public function __construct(
        public readonly array $times,
    ) {}

    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable
    {
        $sorted = $this->times;
        sort($sorted);

        foreach ($sorted as $time) {
            $candidate = $current->setTimeFromTimeString($time);
            if ($candidate->greaterThan($current)) {
                return $candidate;
            }
        }

        return $current->addDay()->setTimeFromTimeString($sorted[0]);
    }

    public function label(): string
    {
        return 'Codziennie o '.implode(', ', $this->times);
    }

    public function toArray(): array
    {
        return ['type' => 'at_times_of_day', 'times' => $this->times];
    }
}
