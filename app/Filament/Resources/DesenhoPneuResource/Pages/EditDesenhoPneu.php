<?php

namespace App\Filament\Resources\DesenhoPneuResource\Pages;

use App\Filament\Resources\DesenhoPneuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDesenhoPneu extends EditRecord
{
    protected static string $resource = DesenhoPneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                // ->successNotification(null),
        ];
    }
}
