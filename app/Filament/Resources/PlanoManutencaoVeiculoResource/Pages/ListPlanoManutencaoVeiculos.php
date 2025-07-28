<?php

namespace App\Filament\Resources\PlanoManutencaoVeiculoResource\Pages;

use App\Filament\Resources\PlanoManutencaoVeiculoResource;
use App\Services\PlanoManutencao\PlanoManutencaoService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanoManutencaoVeiculos extends ListRecords
{
    protected static string $resource = PlanoManutencaoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Gerar PDF')
                ->label('Gerar PDF')
                ->icon('heroicon-o-document-text')
                ->form([
                    \Filament\Forms\Components\TextInput::make('km_tolerancia')
                        ->label('KM Tolerância')
                        ->default(2500)
                        ->numeric()
                        ->required(),
                ])
                ->action(fn($data) => (new PlanoManutencaoService)->gerarRelatorioVencimentoPdf($data['km_tolerancia']))
                ->color('primary'),
        ];
    }
}
