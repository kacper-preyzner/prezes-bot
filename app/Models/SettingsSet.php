<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingsSet extends Model
{
    public const MODEL_GEMINI_FLASH = 'google/gemini-2.5-flash';

    public const MODEL_CLAUDE_SONNET = 'anthropic/claude-sonnet-4';

    public const MODEL_GPT_4_1_MINI = 'openai/gpt-4.1-mini';

    public const AVAILABLE_MODELS = [
        self::MODEL_GEMINI_FLASH => 'Gemini 2.5 Flash',
        self::MODEL_CLAUDE_SONNET => 'Claude Sonnet 4',
        self::MODEL_GPT_4_1_MINI => 'GPT-4.1 Mini',
    ];

    public const DEFAULT_MODEL = self::MODEL_GEMINI_FLASH;

    protected $fillable = [
        'open_router_llm_model',
    ];
}
