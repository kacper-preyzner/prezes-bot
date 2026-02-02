<?php

declare(strict_types=1);

namespace App\Actions;

class GetBSSQuestionById
{
    /**
     * @return array{id: int, question: string, answer: string}|null
     */
    public function handle(int $id): ?array
    {
        foreach (GetRandomBSSQuestion::QUESTIONS as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }
}
