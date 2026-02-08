<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GetSettingsSet;
use App\Models\SettingsSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSettingsSetTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_default_settings_when_none_exist(): void
    {
        $action = new GetSettingsSet;

        $result = $action->handle();

        $this->assertInstanceOf(SettingsSet::class, $result);
        $this->assertDatabaseHas('settings_sets', [
            'open_router_llm_model' => 'google/gemini-2.5-flash',
        ]);
        $this->assertSame('google/gemini-2.5-flash', $result->open_router_llm_model);
    }

    public function test_returns_existing_settings(): void
    {
        SettingsSet::create([
            'open_router_llm_model' => 'anthropic/claude-sonnet-4',
        ]);

        $action = new GetSettingsSet;
        $result = $action->handle();

        $this->assertSame('anthropic/claude-sonnet-4', $result->open_router_llm_model);
    }
}
