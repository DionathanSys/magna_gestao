<?php

namespace App\Filament\Resources\PlanoManutencaoOrdemServicoResource\Pages;

use App\Filament\Resources\PlanoManutencaoOrdemServicoResource;
use App\Services\PreventivaOrdemServico\PreventivaOrdemServicoService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use App\Services\NotificacaoService as notify;
use Illuminate\Support\Facades\Log;

class ListPlanoManutencaoOrdemServicos extends ListRecords
{
    protected static string $resource = PlanoManutencaoOrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->extraModalFooterActions(fn(Actions\Action $action): array => [
                    $action->makeModalSubmitAction('manterVeiculo', arguments: ['another' => true]),
                ])
                ->action(function (Actions\Action $action, Forms\Form $form, array $data, array $arguments): void {
                    Log::debug(__METHOD__ . '-' . __LINE__, ['data' => $data]);
                    $service = new PreventivaOrdemServicoService();
                    $vinculoPlano = $service->create($data);

                    if ($service->hasError()) {
                        notify::error(mensagem: $service->getMessage());
                        $action->halt();
                    }

                    if ($arguments['another'] ?? false) {
                        $form->fill([
                            'veiculo_id'    => $data['veiculo_id'],
                            'km_execucao'   => $data['km_execucao'],
                            'data_execucao' => $data['data_execucao'],

                        ]);
                        $action->halt();
                    }

                    notify::success(mensagem: $service->getMessage());
                })
                ->modalSubmitAction(),
        ];
    }
}
