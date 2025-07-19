<?php

namespace App\Filament\Resources\PlanoManutencaoOrdemServicoResource\Pages;

use App\Filament\Resources\PlanoManutencaoOrdemServicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanoManutencaoOrdemServicos extends ListRecords
{
    protected static string $resource = PlanoManutencaoOrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
