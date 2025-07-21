<?php

namespace App\Filament\Resources\ViagemComplementoResource\Pages;

use App\Filament\Resources\ViagemComplementoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListViagemComplementos extends ListRecords
{
    protected static string $resource = ViagemComplementoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
