<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\SpotifyToken;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;

class SpotifyAuth
{
    public function getAuthorizationUrl(): string
    {
        $params = http_build_query([
            'client_id' => config('services.spotify.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.spotify.redirect_uri'),
            'scope' => 'user-modify-playback-state user-read-playback-state',
        ]);

        return "https://accounts.spotify.com/authorize?{$params}";
    }

    public function exchangeCode(string $code): SpotifyToken
    {
        $response = Http::asForm()->withBasicAuth(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
        )->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.spotify.redirect_uri'),
        ])->throw()->json();

        $token = SpotifyToken::first() ?? new SpotifyToken;
        $token->access_token = $response['access_token'];
        $token->refresh_token = $response['refresh_token'];
        $token->expires_at = CarbonImmutable::now()->addSeconds($response['expires_in']);
        $token->save();

        return $token;
    }

    public function refreshToken(SpotifyToken $token): SpotifyToken
    {
        $response = Http::asForm()->withBasicAuth(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
        )->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
        ])->throw()->json();

        $token->access_token = $response['access_token'];
        if (isset($response['refresh_token'])) {
            $token->refresh_token = $response['refresh_token'];
        }
        $token->expires_at = CarbonImmutable::now()->addSeconds($response['expires_in']);
        $token->save();

        return $token;
    }

    public function getValidToken(): ?SpotifyToken
    {
        $token = SpotifyToken::first();

        if (! $token) {
            return null;
        }

        if ($token->isExpired()) {
            $token = $this->refreshToken($token);
        }

        return $token;
    }
}
