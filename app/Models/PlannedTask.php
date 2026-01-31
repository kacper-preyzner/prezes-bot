<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannedTask extends Model
{
    protected function casts(): array
    {
        return [
            'execute_at' => 'immutable_datetime',
            'repeating' => 'boolean',
        ];
    }
}
