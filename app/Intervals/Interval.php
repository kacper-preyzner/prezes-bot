<?php

declare(strict_types=1);

namespace App\Intervals;

use Carbon\CarbonImmutable;

interface Interval
{
    public function nextExecuteAt(CarbonImmutable $current): CarbonImmutable;

    public function label(): string;

    /** @return array<string, mixed> */
    public function toArray(): array;
}
