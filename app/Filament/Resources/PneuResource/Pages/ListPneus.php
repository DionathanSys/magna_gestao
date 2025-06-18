<?php

namespace App\Filament\Resources\PneuResource\Pages;

use App\Filament\Resources\PneuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPneus extends ListRecords
{
    protected static string $resource = PneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Pneu')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
