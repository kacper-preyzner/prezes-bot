<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Actions\GetSettingsSet;
use App\Models\SettingsSet;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.pages.settings';

    protected static ?string $title = 'Ustawienia';

    protected static ?string $navigationLabel = 'Ustawienia';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(GetSettingsSet::class)->handle();
        $this->form->fill([
            'open_router_llm_model' => $settings->open_router_llm_model,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('open_router_llm_model')
                    ->label('Model AI')
                    ->options(SettingsSet::AVAILABLE_MODELS)
                    ->required()
                    ->native(false)
                    ->helperText('Model używany przez asystenta do odpowiadania na pytania i wykonywania zadań.'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Zapisz')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = app(GetSettingsSet::class)->handle();
        $settings->update([
            'open_router_llm_model' => $data['open_router_llm_model'],
        ]);

        Notification::make()
            ->title('Zapisano')
            ->success()
            ->send();
    }
}
