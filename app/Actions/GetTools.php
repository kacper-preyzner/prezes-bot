<?php

declare(strict_types=1);

namespace App\Actions;

use App\Skills\BssQuizSkill;
use App\Skills\PlannedTaskSkill;
use App\Skills\Skill;
use App\Skills\SpotifySkill;
use App\Skills\TimerSkill;
use App\Skills\WebSearchSkill;

class GetTools
{
    /** @var array<int, class-string<Skill>> */
    protected array $skills = [
        TimerSkill::class,
        PlannedTaskSkill::class,
        WebSearchSkill::class,
        SpotifySkill::class,
        BssQuizSkill::class,
    ];

    /**
     * @return array{tools: array, systemPrompts: array<int, string>}
     */
    public function handle(): array
    {
        $tools = [];
        $systemPrompts = [];

        foreach ($this->skills as $skillClass) {
            /** @var Skill $skill */
            $skill = app($skillClass);

            $tools = array_merge($tools, $skill->tools());

            $prompt = $skill->systemPrompt();
            if ($prompt !== '') {
                $systemPrompts[] = $prompt;
            }
        }

        return ['tools' => $tools, 'systemPrompts' => $systemPrompts];
    }
}
