<?php

namespace App\Filament\Resources\DesenhoPneuResource\Pages;

use App\Filament\Resources\DesenhoPneuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesenhoPneus extends ListRecords
{
    protected static string $resource = DesenhoPneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(null)
                ->label('Desenho Pneu')
                ->icon('heroicon-o-plus-circle')
                ->color('primary'),
        ];
    }
}
