<?php

namespace App\Filament\Resources\PlanoPreventivoResource\Pages;

use App\Filament\Resources\PlanoPreventivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanoPreventivo extends EditRecord
{
    protected static string $resource = PlanoPreventivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                // ->successNotification(null),
        ];
    }
}
