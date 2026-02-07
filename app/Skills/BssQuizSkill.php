<?php

declare(strict_types=1);

namespace App\Skills;

use App\Actions\GetBSSQuestionById;
use App\Actions\GetRandomBSSQuestion;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Tool;

class BssQuizSkill implements Skill
{
    public function __construct(
        protected GetRandomBSSQuestion $getRandomBSSQuestion,
        protected GetBSSQuestionById $getBSSQuestionById,
    ) {}

    public function systemPrompt(): string
    {
        return '';
    }

    public function tools(): array
    {
        return [
            Tool::as('bss_quiz_get_random_question')->for(
                'Gets a random BSS quiz question. Use when user asks for a BSS question. IMPORTANT: You MUST say the question to the user in format "Pytanie {id}: {question}" and wait for their answer. After user answers, use bss_quiz_check_answer to verify.',
            )->withNumberParameter(
                'count',
                'How many questions to return (ignored, always returns 1)',
            )->using(function (int $count = 1): string {
                Log::debug('bss_quiz_get_random_question called');

                $result = $this->getRandomBSSQuestion->handle();

                return "Question {$result['id']}: {$result['question']}";
            }),
            Tool::as('bss_quiz_check_answer')
                ->for(
                    'Checks if the user\'s answer to a BSS quiz question is correct. Use AFTER the user responds to a question from bss_quiz_get_random_question. Compare the user\'s answer with the correct answer semantically — if the user said the same thing in different words, it IS correct. Only mark wrong if the meaning is fundamentally different. If wrong, show the correct answer.',
                )
                ->withNumberParameter('question_id', 'The question ID from the previously asked question')
                ->withStringParameter('user_answer', 'The answer provided by the user')
                ->using(function (int $question_id, string $user_answer): string {
                    Log::debug('bss_quiz_check_answer called', compact('question_id', 'user_answer'));

                    $question = $this->getBSSQuestionById->handle($question_id);

                    if ($question === null) {
                        return "Question with ID {$question_id} not found.";
                    }

                    return "User answered: \"{$user_answer}\"\nCorrect answer: \"{$question['answer']}\"\nCompare semantically: if the user's answer conveys the same meaning (even with different wording, abbreviations, or less detail), it is CORRECT. Only mark WRONG if the meaning is fundamentally different or incorrect.";
                }),
            Tool::as('bss_quiz_get_question_by_id')->for(
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
            }),
        ];
    }
}
