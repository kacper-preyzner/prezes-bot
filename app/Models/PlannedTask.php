<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\IntervalCast;
use Illuminate\Database\Eloquent\Model;

class PlannedTask extends Model
{
    protected function casts(): array
    {
        return [
            'execute_at' => 'immutable_datetime',
            'interval' => IntervalCast::class,
            'is_running' => 'boolean',
        ];
    }
}
