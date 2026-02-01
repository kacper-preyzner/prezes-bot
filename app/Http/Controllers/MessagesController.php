<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->has('after')) {
            $messages = Message::where('id', '>', (int) $request->input('after'))
                ->orderBy('id')
                ->get();

            return response()->json(['data' => $messages]);
        }

        $query = Message::orderByDesc('id');

        if ($request->has('cursor')) {
            $query->where('id', '<', (int) $request->input('cursor'));
        }

        $messages = $query->limit(20)->get();

        return response()->json([
            'data' => $messages,
            'next_cursor' => $messages->count() === 20 ? $messages->last()->id : null,
        ]);
    }
}
