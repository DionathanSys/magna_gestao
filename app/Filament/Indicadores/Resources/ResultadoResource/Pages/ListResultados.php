<?php

namespace App\Filament\Indicadores\Resources\ResultadoResource\Pages;

use App\Filament\Indicadores\Resources\ResultadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResultados extends ListRecords
{
    protected static string $resource = ResultadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
