<?php

namespace App\Filament\Resources\AgendamentoResource\Pages;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\AgendamentoResource;
use App\Models;
use App\Services\Agendamento\AgendamentoService;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Services\NotificacaoService as notify;

class ListAgendamentos extends ListRecords
{
    protected static string $resource = AgendamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Agendamento')
                ->icon('heroicon-o-plus')
                ->using(function (Actions\Action $action, array $data, string $model): Models\Agendamento {
                    $service = new AgendamentoService();
                    $agendamento = $service->create($data);

                    if ($service->hasError()) {
                        // ds('error no list agendamentos');
                        notify::error('Erro ao criar agendamento.', $service->getMessage());
                        // $action->halt();
                    }

                    return $agendamento;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make(),
            'Em Execução' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', StatusOrdemServicoEnum::EXECUCAO))
                ->badge(Models\Agendamento::query()->where('status', StatusOrdemServicoEnum::EXECUCAO)->count())
                ->badgeColor('info'),
            'Sem Data' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('data_agendamento', null))
                ->badge(Models\Agendamento::query()->where('data_agendamento', null)->count())
                ->badgeColor('info'),
            'Hoje' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('data_agendamento', now()->format('Y-m-d')))
                ->badge(Models\Agendamento::query()->where('data_agendamento', now()->format('Y-m-d'))->count())
                ->badgeColor('info'),
            'Amanhã' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('data_agendamento', now()->addDay()->format('Y-m-d')))
                ->badge(Models\Agendamento::query()
                    ->where('data_agendamento', now()->addDay()->format('Y-m-d'))
                    ->whereIn('status', [StatusOrdemServicoEnum::PENDENTE, StatusOrdemServicoEnum::EXECUCAO])
                    ->count())
                ->badgeColor('info'),
            'Semana' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereBetween('data_agendamento', [
                        now()->startOfWeek()->format('Y-m-d'),
                        now()->endOfWeek()->format('Y-m-d')
                    ])
                ),
            'Atrasados' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('data_agendamento', '<', now()->subDay()->format('Y-m-d'))
                        ->whereIn('status', [StatusOrdemServicoEnum::PENDENTE, StatusOrdemServicoEnum::EXECUCAO])
                )
                ->badge(Models\Agendamento::query()->where('data_agendamento', '<', now()->subDay()->format('Y-m-d'))
                    ->whereIn('status', [StatusOrdemServicoEnum::PENDENTE, StatusOrdemServicoEnum::EXECUCAO])->count())
                ->badgeColor('info'),

        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {

        return 'Hoje';
    }
}
