<?php

namespace App\Filament\Resources\OrdemServicoResource\Pages;

use App\Filament\Resources\OrdemServicoResource;
use App\Models\OrdemServico;
use App\Models\PlanoManutencaoVeiculo;
use App\Models\Veiculo;
use App\Services\OrdemServico\OrdemServicoService;
use Filament\Actions;
use Filament\Facades\Filament;
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
            Actions\Action::make('manutencao-preventiva')
                ->label('Manutenção Preventiva')
                ->form(fn(\Filament\Forms\Form $form) => $form
                    ->schema([
                        \Filament\Forms\Components\Select::make('plano_preventivo_id')
                            ->label('Plano Preventivo')
                            ->options(
                                Veiculo::find($this->record->veiculo_id)
                                    ->planoPreventivo()
                                    ->pluck('descricao', 'plano_preventivo_id')
                            )
                            ->required()
                    ]))
                ->action(function (OrdemServico $record, array $data) {
                    dd('Manutenção Preventiva', $record, $data);
                })
                ->color('primary')
                ->icon('heroicon-o-wrench')
        ];
    }
}
