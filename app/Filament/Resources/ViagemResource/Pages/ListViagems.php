<?php

namespace App\Filament\Resources\ViagemResource\Pages;

use App\Filament\Resources\ViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListViagems extends ListRecords
{
    protected static string $resource = ViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\CreateAction::make(),
                Actions\Action::make('importar-viagens')
                    ->label('Importar Viagens')
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->url(route('import.viagens'))
                    ->color('primary'),
            ])

        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         \App\Filament\Resources\ViagemResource\Widgets\AdvancedStatsOverviewWidget::class,
    //     ];
    // }
}
