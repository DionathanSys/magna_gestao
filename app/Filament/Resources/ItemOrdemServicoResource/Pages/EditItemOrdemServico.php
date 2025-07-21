<?php

namespace App\Filament\Resources\ItemOrdemServicoResource\Pages;

use App\Filament\Resources\ItemOrdemServicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemOrdemServico extends EditRecord
{
    protected static string $resource = ItemOrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(null),
        ];
    }
}
