<?php

namespace App\Filament\Resources\CargaViagemResource\Pages;

use App\Filament\Resources\CargaViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCargaViagems extends ListRecords
{
    protected static string $resource = CargaViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
