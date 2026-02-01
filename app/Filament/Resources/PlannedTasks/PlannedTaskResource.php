<?php

namespace App\Filament\Resources\PlannedTasks;

use App\Filament\Resources\PlannedTasks\Pages\CreatePlannedTask;
use App\Filament\Resources\PlannedTasks\Pages\EditPlannedTask;
use App\Filament\Resources\PlannedTasks\Pages\ListPlannedTasks;
use App\Filament\Resources\PlannedTasks\Schemas\PlannedTaskForm;
use App\Filament\Resources\PlannedTasks\Tables\PlannedTasksTable;
use App\Models\PlannedTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlannedTaskResource extends Resource
{
    protected static ?string $model = PlannedTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Zaplanowane zadanie';

    protected static ?string $pluralModelLabel = 'Zaplanowane zadania';

    public static function form(Schema $schema): Schema
    {
        return PlannedTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlannedTasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlannedTasks::route('/'),
            'create' => CreatePlannedTask::route('/create'),
            'edit' => EditPlannedTask::route('/{record}/edit'),
        ];
    }
}
