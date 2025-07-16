<?php

namespace App\Filament\Resources\HistoricoQuilometragemResource\Pages;

use App\Filament\Resources\HistoricoQuilometragemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoricoQuilometragem extends EditRecord
{
    protected static string $resource = HistoricoQuilometragemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
