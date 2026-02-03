<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\SettingsSet;

class GetSettingsSet
{
    public function handle(): SettingsSet
    {
        return SettingsSet::firstOrCreate([], [
            'open_router_llm_model' => SettingsSet::DEFAULT_MODEL,
        ]);
    }
}
