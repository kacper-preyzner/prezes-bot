<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\PushToken;

class RegisterPushToken
{
    public function handle(string $token): PushToken
    {
        return PushToken::updateOrCreate(['token' => $token]);
    }
}
