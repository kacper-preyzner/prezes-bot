<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AskAI;
use App\Http\Requests\AskAIRequest;

class AskAIController extends Controller
{
    public function __invoke(AskAIRequest $request, AskAI $askAI)
    {
        $result = $askAI->handle($request->validated('prompt'));

        return response()->json([
            'message' => $result['assistantMessage']->content,
            'user_message' => $result['userMessage'],
            'assistant_message' => $result['assistantMessage'],
            'actions' => $result['actions'],
        ]);
    }
}
