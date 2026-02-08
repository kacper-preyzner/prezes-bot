<?php

declare(strict_types=1);

namespace App\Skills;

use App\Actions\PlaySpotify;
use App\Data\ActionCollector;
use App\Data\ActionData;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Tool;

class SpotifySkill implements Skill
{
    public function __construct(
        protected PlaySpotify $playSpotify,
        protected ActionCollector $actionCollector,
    ) {}

    public function systemPrompt(): string
    {
        return 'Kiedy użytkownik prosi o puszczenie muzyki/piosenki, ZAWSZE użyj narzędzia play_spotify.';
    }

    public function tools(): array
    {
        return [
            Tool::as('play_spotify')->for(
                'Play a song/track on the user\'s Spotify. Use when the user asks to play music/song/piosenka/utwór.',
            )->withStringParameter(
                'query',
                'Search query — song name, artist, or both',
            )->using(function (string $query): string {
                Log::debug('play_spotify called', ['query' => $query]);

                try {
                    $result = $this->playSpotify->handle($query);
                    $this->actionCollector->add(new ActionData(
                        type: 'spotify_playing',
                        track: $result['track'],
                        artist: $result['artist'],
                    ));

                    return "Playing: {$result['artist']} — {$result['track']}";
                } catch (\RuntimeException $e) {
                    return "Spotify error: {$e->getMessage()}";
                }
            }),
        ];
    }
}
