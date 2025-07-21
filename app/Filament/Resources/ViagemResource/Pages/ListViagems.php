<?php

namespace App\Filament\Resources\ViagemResource\Pages;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ViagemResource;
use App\Models\Viagem;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class ListViagems extends ListRecords
{
    protected static string $resource = ViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('importar-viagens')
                    ->label('Importar Viagens')
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->url(route('import.viagens'))
                    ->color('primary'),
            ])
            ->label('Ações'),
            BulkAction::make('conferido')
                ->label('Conferir')
                ->icon('heroicon-o-check-circle')
                ->action(function (Collection $records) {
                    $records->each(function (Viagem $record) {
                        $record->conferido = true;
                        $record->save();
                    });
                })
                ->requiresConfirmation(),
            FilamentExportBulkAction::make('export')
                ->fileName('Viagens')
                ->disableAdditionalColumns()
                ->pageOrientationFieldLabel('Page Orientation') // Label for page orientation input
                ->filterColumnsFieldLabel('filter columns')
        ];
    }
}
