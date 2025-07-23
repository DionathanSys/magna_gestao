<?php

namespace App\Filament\Resources\VeiculoResource\Pages;

use App\Filament\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditVeiculo extends EditRecord
{
    protected static string $resource = VeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                // ->successNotification(null)
                ->visible(fn() => Auth::user()->is_admin),
        ];
    }

    // protected function fillForm(): void
    // {
    //     $this->form->fill([
    //         'placa'             => $this->record->placa,
    //         'is_active'         => $this->record->is_active,
    //     ]);
    // }

}
