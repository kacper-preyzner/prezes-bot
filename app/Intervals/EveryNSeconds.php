<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

class EveryNSeconds implements Interval
{
    public function __construct(
        public readonly int $n,
    ) {}

    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable
    {
        return $current->addSeconds($this->n);
    }

    public function label(): string
    {
        return "Co {$this->n} sekund";
    }

    public function toArray(): array
    {
        return ['type' => 'every_n_seconds', 'n' => $this->n];
    }
}
