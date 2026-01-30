<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Token;
use Illuminate\Support\Str;

class CreateToken
{
    public function handle(): Token
    {
        do {
            $token = Str::random(100);
        } while (Token::where('token', $token)->exists());

        return Token::create(['token' => $token]);
    }
}
