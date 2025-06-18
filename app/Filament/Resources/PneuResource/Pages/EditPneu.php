<?php

namespace App\Filament\Resources\PneuResource\Pages;

use App\Filament\Resources\PneuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPneu extends EditRecord
{
    protected static string $resource = PneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
