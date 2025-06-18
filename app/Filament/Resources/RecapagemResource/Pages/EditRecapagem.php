<?php

namespace App\Filament\Resources\RecapagemResource\Pages;

use App\Filament\Resources\RecapagemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecapagem extends EditRecord
{
    protected static string $resource = RecapagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
