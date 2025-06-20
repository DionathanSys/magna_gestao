<?php

namespace App\Filament\Resources\VeiculoResource\Pages;

use App\Filament\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVeiculo extends CreateRecord
{
    protected static string $resource = VeiculoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['km_movimento']);
        unset($data['data_movimento']);

        return $data;
    }
}
