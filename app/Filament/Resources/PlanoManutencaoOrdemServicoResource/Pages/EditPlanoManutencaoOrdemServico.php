<?php

namespace App\Filament\Resources\PlanoManutencaoOrdemServicoResource\Pages;

use App\Filament\Resources\PlanoManutencaoOrdemServicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanoManutencaoOrdemServico extends EditRecord
{
    protected static string $resource = PlanoManutencaoOrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
