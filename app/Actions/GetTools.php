<?php

declare(strict_types=1);

namespace App\Actions;

use App\Intervals\IntervalFactory;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Tool;

class GetTools
{
    public function __construct(
        protected CreatePlannedTask $createPlannedTask,
        protected WebSearch $webSearch,
        protected PlaySpotify $playSpotify,
        protected GetRandomBSSQuestion $getRandomBSSQuestion,
        protected GetBSSQuestionById $getBSSQuestionById,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $actions  Collected client-side actions
     */
    public function handle(array &$actions = []): array
    {
        $setTimerTool = Tool::as('set_timer')
            ->for(
                'Set a countdown timer on the user\'s device. Use when user asks to set a timer/minutnik/stoper. Use this ONLY IF USER TELLS YOU SPECIFICALLY TO USE timer/minutnik/stoper.',
            )
            ->withNumberParameter('seconds', 'Timer duration in seconds')
            ->withStringParameter('message', 'Short label for the timer, e.g. "Jajka", "Przerwa"')
            ->using(function (int $seconds, string $message) use (&$actions): string {
                Log::debug('set_timer called', compact('seconds', 'message'));

                $actions[] = ['type' => 'set_timer', 'seconds' => $seconds, 'message' => $message];

                return "Timer set for {$seconds} seconds with message: {$message}";
            });

        $createPlannedTaskTool = Tool::as('create_planned_task')
            ->for(
                'Plan task to execute later. IMPORTANT: Always create exactly ONE task per user request. If the user wants something on multiple days/times, use on_days_at_times with a schedule covering all days and times — do NOT create separate tasks.',
            )
            ->withStringParameter(
                'instruction',
                'Instruction for the AI that will handle this task later, e.g. "Przypomnij użytkownikowi żeby umył zęby". Should be a directive for the AI, not a direct task description.',
            )
            ->withStringParameter('executeAt', 'Timestamp of when to execute the task')
            ->withStringParameter('interval', <<<'DESC'
            Optional JSON for repeating tasks. null = one-time. EVERY object MUST have a "type" key. Available types:
            - {"type":"every_n_seconds","n":30}
            - {"type":"every_n_minutes","n":5}
            - {"type":"at_times_of_day","times":["08:00","20:00"]} — same times every day
            - {"type":"every_week_at","day":1,"time":"09:00"} — single day+time weekly (day: 0=Sun,1=Mon,...,6=Sat)
            - {"type":"every_month_at","day":15,"time":"10:00"}
            - {"type":"on_days_at_times","schedule":{"4":["14:00","20:00"],"5":["16:00"]}} — PREFERRED for multiple days/times per week (keys: 0=Sun..6=Sat). Use this instead of creating multiple tasks!
            DESC)
            ->using(function (string $instruction, string $executeAt, ?string $interval = null): string {
                Log::debug('create_planned_task called', compact('instruction', 'executeAt', 'interval'));

                $parsedInterval = null;
                if ($interval !== null && $interval !== 'null') {
                    $decoded = json_decode($interval, true);

                    if ($decoded === null) {
                        // AI sometimes sends single-quoted JSON
                        $decoded = json_decode(str_replace("'", '"', $interval), true);
                    }

                    if ($decoded !== null) {
                        $parsedInterval = IntervalFactory::fromArray($decoded);
                    }
                }

                $task = $this->createPlannedTask->handle(
                    $instruction,
                    CarbonImmutable::parse($executeAt, 'Europe/Warsaw'),
                    $parsedInterval,
                );

                return "Task created: {$task->instruction} scheduled at {$task->execute_at}";
            });

        $webSearchTool = Tool::as('web_search')->for(
            'Search the web for current information, news, trends, etc. Returns AI-synthesized answer with citations.',
        )->withStringParameter('query', 'The search query')->using(function (string $query): string {
            Log::debug('web_search called', ['query' => $query]);

            return $this->webSearch->handle($query);
        });

        $playSpotifyTool = Tool::as('play_spotify')->for(
            'Play a song/track on the user\'s Spotify. Use when the user asks to play music/song/piosenka/utwór.',
        )->withStringParameter(
            'query',
            'Search query — song name, artist, or both',
        )->using(function (string $query) use (&$actions): string {
            Log::debug('play_spotify called', ['query' => $query]);

            try {
                $result = $this->playSpotify->handle($query);
                $actions[] = ['type' => 'spotify_playing', 'track' => $result['track'], 'artist' => $result['artist']];

                return "Playing: {$result['artist']} — {$result['track']}";
            } catch (\RuntimeException $e) {
                return "Spotify error: {$e->getMessage()}";
            }
        });

        $bssRandomQuestionTool = Tool::as('bss_quiz_get_random_question')->for(
            'Gets a random BSS quiz question. Use when user asks for a BSS question. IMPORTANT: You MUST say the question to the user in format "Pytanie {id}: {question}" and wait for their answer. After user answers, use bss_quiz_check_answer to verify.',
        )->withNumberParameter(
            'count',
            'How many questions to return (ignored, always returns 1)',
        )->using(function (int $count = 1): string {
            Log::debug('bss_quiz_get_random_question called');

            $result = $this->getRandomBSSQuestion->handle();

            return "Question {$result['id']}: {$result['question']}";
        });

        $bssCheckAnswerTool = Tool::as('bss_quiz_check_answer')->for(
            'Checks if the user\'s answer to a BSS quiz question is correct. Use AFTER the user responds to a question from bss_quiz_get_random_question. Compare the user\'s answer with the correct answer semantically — if the user said the same thing in different words, it IS correct. Only mark wrong if the meaning is fundamentally different. If wrong, show the correct answer.',
        )->withNumberParameter(
            'question_id',
            'The question ID from the previously asked question',
        )->withStringParameter(
            'user_answer',
            'The answer provided by the user',
        )->using(function (int $question_id, string $user_answer): string {
            Log::debug('bss_quiz_check_answer called', compact('question_id', 'user_answer'));

            $question = $this->getBSSQuestionById->handle($question_id);

            if ($question === null) {
                return "Question with ID {$question_id} not found.";
            }

            return "User answered: \"{$user_answer}\"\nCorrect answer: \"{$question['answer']}\"\nCompare semantically: if the user's answer conveys the same meaning (even with different wording, abbreviations, or less detail), it is CORRECT. Only mark WRONG if the meaning is fundamentally different or incorrect.";
        });

        $bssGetQuestionByIdTool = Tool::as('bss_quiz_get_question_by_id')->for(
            'Gets a specific BSS quiz question by its ID. Use when you need to retrieve a question you asked before.',
        )->withNumberParameter(
            'question_id',
            'The question ID to retrieve',
        )->using(function (int $question_id): string {
            Log::debug('bss_quiz_get_question_by_id called', compact('question_id'));

            $question = $this->getBSSQuestionById->handle($question_id);

            if ($question === null) {
                return "Question with ID {$question_id} not found.";
            }

            return "Question {$question['id']}: {$question['question']}\nAnswer: {$question['answer']}";
        });

        return [$setTimerTool, $createPlannedTaskTool, $webSearchTool, $playSpotifyTool, $bssRandomQuestionTool, $bssCheckAnswerTool, $bssGetQuestionByIdTool];
    }
}
