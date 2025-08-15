<?php

namespace App\Filament\Indicadores\Resources\ResultadoResource\Pages;

use App\Filament\Indicadores\Resources\ResultadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResultado extends EditRecord
{
    protected static string $resource = ResultadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
