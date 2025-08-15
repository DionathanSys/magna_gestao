<?php

namespace App\Filament\Indicadores\Resources\GestorResource\Pages;

use App\Filament\Indicadores\Resources\GestorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGestors extends ListRecords
{
    protected static string $resource = GestorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
