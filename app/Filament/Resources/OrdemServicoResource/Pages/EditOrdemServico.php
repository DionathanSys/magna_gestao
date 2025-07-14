<?php

namespace App\Filament\Resources\OrdemServicoResource\Pages;

use App\Filament\Resources\OrdemServicoResource;
use App\Models\OrdemServico;
use App\Services\OrdemServico\OrdemServicoService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditOrdemServico extends EditRecord
{
    protected static string $resource = OrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => Auth::user()->is_admin),
            Actions\Action::make('encerrar')
                ->label('Encerrar OS')
                ->action(function (OrdemServico $record) {
                    (new OrdemServicoService)->encerrarOrdemServico($record);
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->after(fn() => $this->refreshFormData([
                    'status',
                    'data_fim',
                ])),
        ];
    }
}
