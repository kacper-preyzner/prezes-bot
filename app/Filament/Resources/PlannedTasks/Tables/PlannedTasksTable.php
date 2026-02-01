<?php

namespace App\Filament\Resources\PlannedTasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PlannedTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('instruction')->searchable()->limit(50),
                TextColumn::make('execute_at')->dateTime()->sortable(),
                IconColumn::make('repeating')->boolean(),
                IconColumn::make('is_running')->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('repeating'),
                TernaryFilter::make('is_running'),
            ])
            ->recordActions([
                ViewAction::make()->color(Color::Purple),
                EditAction::make()->color('info'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
