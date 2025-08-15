<?php

namespace App\Filament\Indicadores\Resources\IndicadorResource\Pages;

use App\Filament\Indicadores\Resources\IndicadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndicadors extends ListRecords
{
    protected static string $resource = IndicadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
