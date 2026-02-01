<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushToken extends Model
{
    protected $fillable = ['token'];
}
