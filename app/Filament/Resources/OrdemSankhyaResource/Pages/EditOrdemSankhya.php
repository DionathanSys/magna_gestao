<?php

namespace App\Filament\Resources\OrdemSankhyaResource\Pages;

use App\Filament\Resources\OrdemSankhyaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdemSankhya extends EditRecord
{
    protected static string $resource = OrdemSankhyaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
