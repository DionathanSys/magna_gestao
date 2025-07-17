<?php

namespace App\Filament\Resources\OrdemServicoResource\Pages;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\OrdemServicoResource;
use App\Models\OrdemServico;
use App\Models\Veiculo;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Services\NotificacaoService as notify;
use Illuminate\Support\Facades\Auth;

class ListOrdemServicos extends ListRecords
{
    protected static string $resource = OrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('OS')
                ->icon('heroicon-o-plus')
                ->before(function (Actions\CreateAction $action, array $data) {
                    $veiculo = Veiculo::with('kmAtual')->find($data['veiculo_id']);
                    if ($veiculo->kmAtual->quilometragem > $data['quilometragem']) {
                        notify::error('A quilometragem informada deve ser maior ou igual à quilometragem atual do veículo.');
                        $action->halt();
                    }
                })
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = Auth::user()->id;
                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make(),
            'pendente' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', StatusOrdemServicoEnum::PENDENTE)),
            'concluído' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', StatusOrdemServicoEnum::CONCLUIDO)),
            'abrir_ordem' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_sankhya', StatusOrdemServicoEnum::PENDENTE))
                ->badge(OrdemServico::query()->where('status_sankhya', StatusOrdemServicoEnum::PENDENTE)->count())
                ->badgeColor('info'),
            'encerrar_ordem' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', StatusOrdemServicoEnum::CONCLUIDO)
                                                            ->where('status_sankhya', '!=',StatusOrdemServicoEnum::CONCLUIDO))
                                                            ->badge(OrdemServico::query()
                                                                ->where('status', StatusOrdemServicoEnum::CONCLUIDO)
                                                                ->where('status_sankhya', '!=',StatusOrdemServicoEnum::CONCLUIDO)->count())
                                                            ->badgeColor('info'),

        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        if(Auth::user()->name == 'Angelica'){
            return 'abrir_ordem';
        }

        return 'pendente';

    }
}
