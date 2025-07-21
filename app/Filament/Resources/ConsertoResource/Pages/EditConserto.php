<?php

namespace App\Filament\Resources\ConsertoResource\Pages;

use App\Filament\Resources\ConsertoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConserto extends EditRecord
{
    protected static string $resource = ConsertoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
