<?php

declare(strict_types=1);

namespace Tests\Unit\Skills;

use App\Data\ActionCollector;
use App\Data\ActionData;
use App\Models\SpotifyToken;
use App\Skills\SpotifySkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpotifySkillTest extends TestCase
{
    use RefreshDatabase;

    public function test_play_spotify_tool_plays_track_and_adds_action(): void
    {
        SpotifyToken::create([
            'access_token' => 'test-token',
            'refresh_token' => 'test-refresh',
            'expires_at' => now()->addHour(),
        ]);

        Http::fake([
            'api.spotify.com/v1/search*' => Http::response(['tracks' => ['items' => [
                ['uri' => 'spotify:track:123', 'name' => 'Bohemian Rhapsody', 'artists' => [['name' => 'Queen']]],
            ]]]),
            'api.spotify.com/v1/me/player/play*' => Http::response(null, 204),
        ]);

        $collector = app(ActionCollector::class);
        $skill = app(SpotifySkill::class);
        $tool = $this->findTool($skill->tools(), 'play_spotify');

        $result = $tool->handle(query: 'Bohemian Rhapsody');

        $this->assertStringContainsString('Queen', $result);
        $this->assertStringContainsString('Bohemian Rhapsody', $result);

        $actions = $collector->all();
        $this->assertCount(1, $actions);
        $this->assertInstanceOf(ActionData::class, $actions[0]);
        $this->assertSame('spotify_playing', $actions[0]->type);
        $this->assertSame('Bohemian Rhapsody', $actions[0]->track);
        $this->assertSame('Queen', $actions[0]->artist);
    }

    public function test_play_spotify_tool_returns_error_when_not_connected(): void
    {
        $skill = app(SpotifySkill::class);
        $tool = $this->findTool($skill->tools(), 'play_spotify');

        $result = $tool->handle(query: 'some song');

        $this->assertStringContainsString('Spotify error', $result);

        $collector = app(ActionCollector::class);
        $this->assertCount(0, $collector->all());
    }

    public function test_system_prompt_is_not_empty(): void
    {
        $skill = app(SpotifySkill::class);

        $this->assertNotEmpty($skill->systemPrompt());
    }

    /**
     * @param  array<int, \Prism\Prism\Tool>  $tools
     */
    private function findTool(array $tools, string $name): \Prism\Prism\Tool
    {
        foreach ($tools as $tool) {
            if ($tool->name() === $name) {
                return $tool;
            }
        }

        $this->fail("Tool '{$name}' not found");
    }
}
