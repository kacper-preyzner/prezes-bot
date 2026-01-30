<?php

namespace App\Http\Controllers;

use App\Actions\AskAI;
use App\Http\Requests\AskAIRequest;
use Illuminate\Http\Request;

class AskAIController extends Controller
{
    public function __invoke(AskAIRequest $request, AskAI $askAI)
    {
        $response = $askAI->handle($request->validated('prompt'));

        return response()->json(['message' => $response]);
    }
}
