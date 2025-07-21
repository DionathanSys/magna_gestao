<?php

namespace App\Filament\Resources\IntegradoResource\Pages;

use App\Filament\Resources\IntegradoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntegrado extends EditRecord
{
    protected static string $resource = IntegradoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
