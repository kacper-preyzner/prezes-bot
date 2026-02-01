<?php

declare(strict_types=1);

namespace App\Casts;

use App\Intervals\Interval;
use App\Intervals\IntervalFactory;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<Interval|null, Interval|array|null> */
class IntervalCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Interval
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);

        return IntervalFactory::fromArray($data);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return json_encode($value->toArray());
    }
}
