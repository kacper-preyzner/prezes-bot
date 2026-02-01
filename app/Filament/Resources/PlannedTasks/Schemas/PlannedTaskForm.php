<?php

namespace App\Filament\Resources\PlannedTasks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlannedTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('instruction')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                DateTimePicker::make('execute_at')
                    ->required(),
                Toggle::make('repeating'),
                Toggle::make('is_running')
                    ->disabled(),
            ]);
    }
}
