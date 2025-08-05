<?php

namespace App\Filament\Resources\PlanoManutencaoOrdemServicoResource\Pages;

use App\Filament\Resources\PlanoManutencaoOrdemServicoResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

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
                    // Create

                    if ($arguments['another'] ?? false) {
                            $form->fill([
                                'veiculo_id' => $data['veiculo_id'],

                            ]);
                            $action->halt();
                        }
                }),
        ];
    }
}
