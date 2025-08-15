<?php

namespace App\Filament\Indicadores\Resources\IndicadorResource\Pages;

use App\Filament\Indicadores\Resources\IndicadorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIndicador extends EditRecord
{
    protected static string $resource = IndicadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
