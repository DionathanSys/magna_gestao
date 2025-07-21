<?php

namespace App\Filament\Resources\PlanoManutencaoVeiculoResource\Pages;

use App\Filament\Resources\PlanoManutencaoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanoManutencaoVeiculo extends EditRecord
{
    protected static string $resource = PlanoManutencaoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
