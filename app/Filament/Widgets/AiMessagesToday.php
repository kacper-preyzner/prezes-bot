<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Message;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AiMessagesToday extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '1s';

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Wiadomości AI dzisiaj',
                Message::where('role', 'assistant')->whereDate('created_at', today())->count(),
            ),
            Stat::make(
                'Wiadomości AI łącznie',
                Message::where('role', 'assistant')->count(),
            ),
        ];
    }
}
