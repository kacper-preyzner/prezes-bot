<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Token;

trait WithAuthToken
{
    protected string $authToken = 'test-token-for-testing';

    protected function createAuthToken(): Token
    {
        return Token::create(['token' => $this->authToken]);
    }

    /** @return array<string, string> */
    protected function authHeaders(): array
    {
        return ['Authorization' => $this->authToken];
    }
}
