<?php

namespace App\Filament\Resources\ViagemResource\Pages;

use App\Filament\Resources\ViagemResource;
use App\Filament\Resources\ViagemResource\Widgets\AdvancedStatsOverviewWidget;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Actions\Action;

class Teste extends Page
{
    protected static string $resource = ViagemResource::class;

    protected static ?string $title = 'Page Title';

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Page Title';

    protected static ?string $pluralLabel = 'Page Title';

    protected static ?string $label = 'Page Title';

    protected static string $view = 'filament.resources.viagem-resource.pages.teste';

    use InteractsWithRecord;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit'),
            Action::make('delete')
                ->requiresConfirmation()
                ->action(fn () => dd(1)),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdvancedStatsOverviewWidget::class
        ];
    }
}
