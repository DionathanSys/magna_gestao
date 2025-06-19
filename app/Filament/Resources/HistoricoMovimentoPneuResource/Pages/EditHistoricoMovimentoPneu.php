<?php

namespace App\Filament\Resources\HistoricoMovimentoPneuResource\Pages;

use App\Filament\Resources\HistoricoMovimentoPneuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoricoMovimentoPneu extends EditRecord
{
    protected static string $resource = HistoricoMovimentoPneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
