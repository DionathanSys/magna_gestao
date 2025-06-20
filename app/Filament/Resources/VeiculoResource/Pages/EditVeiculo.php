<?php

namespace App\Filament\Resources\VeiculoResource\Pages;

use App\Filament\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVeiculo extends EditRecord
{
    protected static string $resource = VeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'placa'             => $this->record->placa,
            'is_active'         => $this->record->is_active,
            'km_movimento'      => 1,
            'data_movimento'    => now()->format('Y-m-d'),
        ]);
    }

}
