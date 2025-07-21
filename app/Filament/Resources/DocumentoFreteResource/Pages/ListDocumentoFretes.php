<?php

namespace App\Filament\Resources\DocumentoFreteResource\Pages;

use App\Filament\Resources\DocumentoFreteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentoFretes extends ListRecords
{
    protected static string $resource = DocumentoFreteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
