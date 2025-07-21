<?php

namespace App\Filament\Resources\PlanoManutencaoVeiculoResource\Pages;

use App\Filament\Resources\PlanoManutencaoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanoManutencaoVeiculos extends ListRecords
{
    protected static string $resource = PlanoManutencaoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
