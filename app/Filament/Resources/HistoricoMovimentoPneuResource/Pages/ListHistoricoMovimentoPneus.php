<?php

namespace App\Filament\Resources\HistoricoMovimentoPneuResource\Pages;

use App\Filament\Resources\HistoricoMovimentoPneuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistoricoMovimentoPneus extends ListRecords
{
    protected static string $resource = HistoricoMovimentoPneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
