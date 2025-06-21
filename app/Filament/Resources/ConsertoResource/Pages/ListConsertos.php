<?php

namespace App\Filament\Resources\ConsertoResource\Pages;

use App\Filament\Resources\ConsertoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsertos extends ListRecords
{
    protected static string $resource = ConsertoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Reparo')
                ->icon('heroicon-o-wrench'),
        ];
    }
}
