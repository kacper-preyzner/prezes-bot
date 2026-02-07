<?php

declare(strict_types=1);

namespace App\Models;

use App\Intervals\Interval;
use App\Intervals\IntervalFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property CarbonImmutable $execute_at
 * @property array<string, mixed>|null $interval
 */
class PlannedTask extends Model
{
    protected function casts(): array
    {
        return [
            'execute_at' => 'immutable_datetime',
            'interval' => 'array',
            'is_running' => 'boolean',
        ];
    }

    public function intervalObject(): ?Interval
    {
        if ($this->interval === null) {
            return null;
        }

        return IntervalFactory::fromArray($this->interval);
    }
}
