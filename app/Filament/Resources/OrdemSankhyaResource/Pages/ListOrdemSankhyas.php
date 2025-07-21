<?php

namespace App\Filament\Resources\OrdemSankhyaResource\Pages;

use App\Filament\Resources\OrdemSankhyaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdemSankhyas extends ListRecords
{
    protected static string $resource = OrdemSankhyaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
