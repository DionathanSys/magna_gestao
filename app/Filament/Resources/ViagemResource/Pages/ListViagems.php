<?php

namespace App\Filament\Resources\ViagemResource\Pages;

use App\Filament\Resources\ViagemResource;
use App\Models\Viagem;
use Filament\Actions;
use Filament\Notifications\Notification;
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

                    // ->successNotification(null)
                    ->label('Importar Viagens')
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->url(route('import.viagens'))
                    ->color('primary'),
            ])
            ->label('Ações'),
            Actions\Action::make('teste')
                ->label('Teste')
                ->icon('heroicon-o-arrow-up-on-square')
                ->color('primary')
                ->action(function (BulkAction $action, Collection $records) {
                    Notification::make()
                        ->title('Ação de Teste')
                        ->body('Você acionou a ação de teste com  registros.')
                        ->success()
                        ->send();
                    return;
                })
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion(),

        ];
    }
}
