<?php

namespace App\Filament\Resources\HistoricoQuilometragemResource\Pages;

use App\Filament\Resources\HistoricoQuilometragemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistoricoQuilometragems extends ListRecords
{
    protected static string $resource = HistoricoQuilometragemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
