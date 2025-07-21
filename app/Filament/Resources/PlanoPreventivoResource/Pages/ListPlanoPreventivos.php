<?php

namespace App\Filament\Resources\PlanoPreventivoResource\Pages;

use App\Filament\Resources\PlanoPreventivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanoPreventivos extends ListRecords
{
    protected static string $resource = PlanoPreventivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
