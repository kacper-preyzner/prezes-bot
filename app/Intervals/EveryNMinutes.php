<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

class EveryNMinutes implements Interval
{
    public function __construct(
        public readonly int $n,
    ) {}

    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable
    {
        return $current->addMinutes($this->n);
    }

    public function label(): string
    {
        return "Co {$this->n} minut";
    }

    public function toArray(): array
    {
        return ['type' => 'every_n_minutes', 'n' => $this->n];
    }
}
