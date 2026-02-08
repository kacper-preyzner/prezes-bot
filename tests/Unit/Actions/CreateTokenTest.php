<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateToken;
use App\Models\Token;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_unique_token(): void
    {
        $action = new CreateToken;

        $token = $action->handle();

        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue(Token::where('token', $token->token)->exists());
        $this->assertSame(100, strlen($token->token));
    }

    public function test_creates_different_tokens(): void
    {
        $action = new CreateToken;

        $token1 = $action->handle();
        $token2 = $action->handle();

        $this->assertNotSame($token1->token, $token2->token);
    }
}
