<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\PushToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotification
{
    public function handle(string $title, string $body): void
    {
        $tokens = PushToken::pluck('token')->all();

        if (empty($tokens)) {
            Log::warning('SendPushNotification: no push tokens registered');

            return;
        }

        $messages = array_map(fn (string $token) => [
            'to' => $token,
            'title' => $title,
            'body' => $body,
            'sound' => 'notification.wav',
            'channelId' => 'default',
        ], $tokens);

        $response = Http::post('https://exp.host/--/api/v2/push/send', $messages);

        if ($response->failed()) {
            Log::error('SendPushNotification: Expo push API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return;
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            return;
        }

        foreach ($data as $i => $ticket) {
            if (($ticket['status'] ?? null) === 'error' && ($ticket['details']['error'] ?? null) === 'DeviceNotRegistered') {
                PushToken::where('token', $tokens[$i])->delete();
                Log::info('SendPushNotification: removed invalid token', ['token' => $tokens[$i]]);
            }
        }
    }
}
