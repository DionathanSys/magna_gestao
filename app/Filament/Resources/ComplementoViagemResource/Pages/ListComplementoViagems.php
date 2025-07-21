<?php

namespace App\Filament\Resources\ComplementoViagemResource\Pages;

use App\Filament\Resources\ComplementoViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplementoViagems extends ListRecords
{
    protected static string $resource = ComplementoViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
