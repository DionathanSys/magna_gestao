<?php

namespace App\Filament\Resources\AgendamentoResource\Pages;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\AgendamentoResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListAgendamentos extends ListRecords
{
    protected static string $resource = AgendamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = Auth::user()->id;
                    $data['updated_by'] = Auth::user()->id;
                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make(),
            'Hoje' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('data_agendamento', now()->format('Y-m-d'))),
            'Amanhã' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('data_agendamento', now()->addDay()->format('Y-m-d'))),
            'Semana' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereBetween('data_agendamento', [
                        now()->startOfWeek()->format('Y-m-d'),
                        now()->endOfWeek()->format('Y-m-d')
                    ])
                ),

        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {

        return 'Hoje';
    }
}
