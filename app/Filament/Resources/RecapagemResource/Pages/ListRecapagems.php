<?php

namespace App\Filament\Resources\RecapagemResource\Pages;

use App\Filament\Resources\RecapagemResource;
use App\Models\Recapagem;
use App\Services\Pneus\PneuService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecapagems extends ListRecords
{
    protected static string $resource = RecapagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            // ->successNotification(null)
                ->label('Registro Recap')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->after(fn(Recapagem $record) => PneuService::atualizarCicloVida($record)),
        ];
    }
}
