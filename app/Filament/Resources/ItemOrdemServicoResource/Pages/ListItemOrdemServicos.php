<?php

namespace App\Filament\Resources\ItemOrdemServicoResource\Pages;

use App\Filament\Resources\ItemOrdemServicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemOrdemServicos extends ListRecords
{
    protected static string $resource = ItemOrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null),
        ];
    }
}
