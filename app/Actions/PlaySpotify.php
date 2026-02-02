<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class PlaySpotify
{
    public function __construct(
        protected SpotifyAuth $spotifyAuth,
    ) {}

    /**
     * @return array{track: string, artist: string}
     */
    public function handle(string $query): array
    {
        $token = $this->spotifyAuth->getValidToken();

        if (! $token) {
            throw new \RuntimeException('Spotify nie jest połączony. Połącz Spotify w aplikacji.');
        }

        $search = Http::withToken($token->access_token)
            ->get('https://api.spotify.com/v1/search', [
                'q' => $query,
                'type' => 'track',
                'limit' => 1,
                'market' => 'PL',
            ])->throw()->json();

        $tracks = $search['tracks']['items'] ?? [];

        if (empty($tracks)) {
            throw new \RuntimeException("Nie znaleziono utworu dla: {$query}");
        }

        $track = $tracks[0];
        $trackUri = $track['uri'];
        $trackName = $track['name'];
        $artistName = $track['artists'][0]['name'] ?? 'Unknown';

        $playResponse = Http::withToken($token->access_token)
            ->put('https://api.spotify.com/v1/me/player/play', [
                'uris' => [$trackUri],
            ]);

        if ($playResponse->status() === 404) {
            $deviceId = $this->findAvailableDevice($token->access_token);

            if (! $deviceId) {
                throw new \RuntimeException('Otwórz Spotify na jakimś urządzeniu.');
            }

            $playResponse = Http::withToken($token->access_token)
                ->put("https://api.spotify.com/v1/me/player/play?device_id={$deviceId}", [
                    'uris' => [$trackUri],
                ]);
        }

        if ($playResponse->status() === 403) {
            throw new \RuntimeException('Spotify Premium jest wymagane do sterowania odtwarzaniem.');
        }

        $playResponse->throw();

        return ['track' => $trackName, 'artist' => $artistName];
    }

    private function findAvailableDevice(string $accessToken): ?string
    {
        $response = Http::withToken($accessToken)
            ->get('https://api.spotify.com/v1/me/player/devices')
            ->json();

        $devices = $response['devices'] ?? [];

        if (empty($devices)) {
            return null;
        }

        // Prefer active device, otherwise pick the first one
        foreach ($devices as $device) {
            if ($device['is_active']) {
                return $device['id'];
            }
        }

        return $devices[0]['id'];
    }
}
