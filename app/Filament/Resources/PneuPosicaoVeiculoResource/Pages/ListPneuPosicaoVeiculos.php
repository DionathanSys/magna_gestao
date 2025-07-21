<?php

namespace App\Filament\Resources\PneuPosicaoVeiculoResource\Pages;

use App\Filament\Resources\PneuPosicaoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPneuPosicaoVeiculos extends ListRecords
{
    protected static string $resource = PneuPosicaoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
