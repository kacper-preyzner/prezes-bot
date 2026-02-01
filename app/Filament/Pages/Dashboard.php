<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Strona główna';

    protected static ?string $navigationLabel = 'Strona główna';
}
