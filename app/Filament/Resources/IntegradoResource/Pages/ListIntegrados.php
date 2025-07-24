<?php

namespace App\Filament\Resources\IntegradoResource\Pages;

use App\Filament\Resources\IntegradoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntegrados extends ListRecords
{
    protected static string $resource = IntegradoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Integrado')
                ->icon('heroicon-o-plus')
                ->color('primary'),
            Actions\Action::make('importar')
                ->label('Importar Integrados')
                ->icon('heroicon-o-cloud-upload')
                ->color('success')
                ->url(route('import.integrados'))
                ->openUrlInNewTab(),
        ];
    }
}
