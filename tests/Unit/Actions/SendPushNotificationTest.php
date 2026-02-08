<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\SendPushNotification;
use App\Models\PushToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_does_nothing_when_no_tokens(): void
    {
        Http::fake();

        $action = new SendPushNotification;
        $action->handle('Title', 'Body');

        Http::assertNothingSent();
    }

    public function test_sends_notification_to_all_tokens(): void
    {
        PushToken::create(['token' => 'ExponentPushToken[token1]']);
        PushToken::create(['token' => 'ExponentPushToken[token2]']);

        Http::fake([
            'exp.host/*' => Http::response(['data' => [
                ['status' => 'ok'],
                ['status' => 'ok'],
            ]]),
        ]);

        $action = new SendPushNotification;
        $action->handle('Title', 'Body');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'exp.host');
        });
    }

    public function test_removes_invalid_tokens(): void
    {
        PushToken::create(['token' => 'ExponentPushToken[invalid]']);

        Http::fake([
            'exp.host/*' => Http::response(['data' => [
                ['status' => 'error', 'details' => ['error' => 'DeviceNotRegistered']],
            ]]),
        ]);

        $action = new SendPushNotification;
        $action->handle('Title', 'Body');

        $this->assertDatabaseEmpty('push_tokens');
    }
}
