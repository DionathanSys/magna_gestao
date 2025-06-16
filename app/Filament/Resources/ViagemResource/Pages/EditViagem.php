<?php

namespace App\Filament\Resources\ViagemResource\Pages;

use App\Filament\Resources\ViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditViagem extends EditRecord
{
    protected static string $resource = ViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Recalcular a viagem após salvar
        (new \App\Services\ViagemService())->recalcularViagem($this->record);
    }
}
