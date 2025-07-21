<?php

namespace App\Filament\Resources\ComplementoViagemResource\Pages;

use App\Filament\Resources\ComplementoViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplementoViagem extends EditRecord
{
    protected static string $resource = ComplementoViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
