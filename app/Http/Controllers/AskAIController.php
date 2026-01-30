<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AskAI;
use App\Http\Requests\AskAIRequest;

class AskAIController extends Controller
{
    public function __invoke(AskAIRequest $request, AskAI $askAI)
    {
        $response = $askAI->handle($request->validated('prompt'));

        return response()->json(['message' => $response]);
    }
}
