<?php

namespace App\Filament\Resources\OrdemServicoResource\Pages;

use App\Filament\Resources\OrdemServicoResource;
use App\Models\OrdemServico;
use App\Services\OrdemServico\OrdemServicoService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdemServico extends EditRecord
{
    protected static string $resource = OrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            // Actions\Action::make('encerrar')
            //     ->label('Encerrar OS')
            //     ->action(function (OrdemServico $record) {
            //         (new OrdemServicoService)->encerrarOrdemServico($record);
            //     })
            //     ->requiresConfirmation()
            //     ->color('success')
            //     ->icon('heroicon-o-check-circle'),
        ];
    }
}
