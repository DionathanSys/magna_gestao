<?php

namespace App\Filament\Resources\DocumentoFreteResource\Pages;

use App\Filament\Resources\DocumentoFreteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentoFrete extends EditRecord
{
    protected static string $resource = DocumentoFreteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
