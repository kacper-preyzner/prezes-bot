<?php

declare(strict_types=1);

namespace App\Data;

class ActionCollector
{
    /** @var array<int, ActionData> */
    protected array $actions = [];

    public function add(ActionData $action): void
    {
        $this->actions[] = $action;
    }

    /** @return array<int, ActionData> */
    public function all(): array
    {
        return $this->actions;
    }
}
