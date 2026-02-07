<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ActionData extends Data
{
    public function __construct(
        public string $type,
        public Optional|int $seconds = new Optional,
        public Optional|string $message = new Optional,
        public Optional|string $track = new Optional,
        public Optional|string $artist = new Optional,
    ) {}
}
