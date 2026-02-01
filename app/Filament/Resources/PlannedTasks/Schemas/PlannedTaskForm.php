<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlannedTasks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
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
                Select::make('interval_type')
                    ->label('Interwał')
                    ->options([
                        '' => 'Jednorazowe',
                        'every_n_seconds' => 'Co N sekund',
                        'every_n_minutes' => 'Co N minut',
                        'at_times_of_day' => 'Codziennie o określonych godzinach',
                        'every_week_at' => 'Co tydzień',
                        'every_month_at' => 'Co miesiąc',
                    ])
                    ->default('')
                    ->live()
                    ->afterStateHydrated(function (Select $component, $state, Get $get) {
                        $interval = $get('interval');
                        if (is_array($interval) && isset($interval['type'])) {
                            $component->state($interval['type']);
                        } elseif (is_string($interval)) {
                            $decoded = json_decode($interval, true);
                            if (is_array($decoded) && isset($decoded['type'])) {
                                $component->state($decoded['type']);
                            }
                        }
                    })
                    ->dehydrated(false),
                TextInput::make('interval_n')
                    ->label('N')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->visible(fn (Get $get) => in_array($get('interval_type'), ['every_n_seconds', 'every_n_minutes']))
                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                        $interval = self::resolveInterval($get('interval'));
                        if (is_array($interval) && isset($interval['n'])) {
                            $component->state($interval['n']);
                        }
                    })
                    ->dehydrated(false),
                TextInput::make('interval_times')
                    ->label('Godziny (oddzielone przecinkami, np. 08:00,14:00,20:00)')
                    ->required()
                    ->visible(fn (Get $get) => $get('interval_type') === 'at_times_of_day')
                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                        $interval = self::resolveInterval($get('interval'));
                        if (is_array($interval) && isset($interval['times'])) {
                            $component->state(implode(',', $interval['times']));
                        }
                    })
                    ->dehydrated(false),
                Select::make('interval_day_of_week')
                    ->label('Dzień tygodnia')
                    ->options([
                        0 => 'Niedziela',
                        1 => 'Poniedziałek',
                        2 => 'Wtorek',
                        3 => 'Środa',
                        4 => 'Czwartek',
                        5 => 'Piątek',
                        6 => 'Sobota',
                    ])
                    ->required()
                    ->visible(fn (Get $get) => $get('interval_type') === 'every_week_at')
                    ->afterStateHydrated(function (Select $component, Get $get) {
                        $interval = self::resolveInterval($get('interval'));
                        if (is_array($interval) && isset($interval['day'])) {
                            $component->state($interval['day']);
                        }
                    })
                    ->dehydrated(false),
                TextInput::make('interval_day_of_month')
                    ->label('Dzień miesiąca')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31)
                    ->required()
                    ->visible(fn (Get $get) => $get('interval_type') === 'every_month_at')
                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                        $interval = self::resolveInterval($get('interval'));
                        if (is_array($interval) && isset($interval['day'])) {
                            $component->state($interval['day']);
                        }
                    })
                    ->dehydrated(false),
                TextInput::make('interval_time')
                    ->label('Godzina (np. 09:00)')
                    ->required()
                    ->visible(fn (Get $get) => in_array($get('interval_type'), ['every_week_at', 'every_month_at']))
                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                        $interval = self::resolveInterval($get('interval'));
                        if (is_array($interval) && isset($interval['time'])) {
                            $component->state($interval['time']);
                        }
                    })
                    ->dehydrated(false),
                Toggle::make('is_running')
                    ->disabled(),
            ]);
    }

    private static function resolveInterval(mixed $interval): ?array
    {
        if (is_array($interval)) {
            return isset($interval['type']) ? $interval : null;
        }

        if (is_string($interval)) {
            $decoded = json_decode($interval, true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }
}
