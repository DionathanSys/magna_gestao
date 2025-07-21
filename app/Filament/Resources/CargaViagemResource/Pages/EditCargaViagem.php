<?php

namespace App\Filament\Resources\CargaViagemResource\Pages;

use App\Filament\Resources\CargaViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCargaViagem extends EditRecord
{
    protected static string $resource = CargaViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
