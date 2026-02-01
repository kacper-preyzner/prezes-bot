<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\RegisterPushToken;
use App\Http\Requests\RegisterPushTokenRequest;

class RegisterPushTokenController extends Controller
{
    public function __invoke(RegisterPushTokenRequest $request, RegisterPushToken $registerPushToken)
    {
        $registerPushToken->handle($request->validated('token'));

        return response()->json(['status' => 'ok']);
    }
}
