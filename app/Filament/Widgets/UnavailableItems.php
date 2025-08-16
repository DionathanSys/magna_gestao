<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VeiculoResource;
use App\Models\PlanoManutencaoVeiculo;
use App\Models\Veiculo;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UnavailableItems extends BaseWidget
{
    protected static string $view = 'filament.widgets.unavailable-items';

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Planos Preventivos Abaixo de 3.000 KM';

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    protected int $queryCount;

    protected int $perPage = 10;

    public function boot()
    {
        $this->queryCount = $this->getBaseQuery()->count();
    }

    public function getColumnSpan(): int | string | array
    {
        return 6;
    }

    protected function getBaseQuery(): Builder
    {
        return PlanoManutencaoVeiculo::query()
        ->join('veiculos', 'planos_manutencao_veiculo.veiculo_id', '=', 'veiculos.id')
        ->join('planos_preventivo', 'planos_manutencao_veiculo.plano_preventivo_id', '=', 'planos_preventivo.id')
        ->leftJoin('planos_manutencao_ordem_servico as pmos', function($join) {
            $join->on('planos_manutencao_veiculo.plano_preventivo_id', '=', 'pmos.plano_preventivo_id')
                 ->on('planos_manutencao_veiculo.veiculo_id', '=', 'pmos.veiculo_id');
        })
        ->leftJoin('historico_quilometragens as hq', function($join) {
            $join->on('veiculos.id', '=', 'hq.veiculo_id')
                 ->whereRaw('hq.id = (SELECT MAX(id) FROM historico_quilometragens WHERE veiculo_id = veiculos.id)');
        })
        ->selectRaw('
            planos_manutencao_veiculo.*,
            planos_preventivo.descricao as planos_preventivo,
            veiculos.placa,
            veiculos.modelo,
            veiculos.is_active,
            COALESCE(MAX(pmos.km_execucao), 0) + planos_preventivo.intervalo as proxima_execucao,
            (COALESCE(MAX(pmos.km_execucao), 0) + planos_preventivo.intervalo) - COALESCE(hq.quilometragem, 0) as quilometragem_restante
        ')
        ->groupBy('planos_manutencao_veiculo.id', 'veiculos.id', 'planos_preventivo.id', 'hq.quilometragem')
        ->havingRaw('quilometragem_restante <= 3001');
    }

    protected function makeTable(): Table
    {
        return $this->makeBaseTable()
            ->heading(null);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption($this->perPage)
            ->paginated(fn() => $this->queryCount > $this->perPage)
            ->query($this->getBaseQuery())
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.km_medio')
                    ->label('KM Médio')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.km_atual.quilometragem')
                    ->label('KM Atual')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planoPreventivo.descricao')
                    ->label('Plano')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quilometragem_restante')
                    ->label('Quilometragem Restante')
                    ->numeric(0, ',', '.')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->quilometragem_restante <= 0 => 'danger',
                        $record->quilometragem_restante <= 500 => 'warning',
                        $record->quilometragem_restante <= 1000 => 'info',
                        $record->quilometragem_restante <= 2000 => 'primary',
                        default => 'success'
                    }),
            ])
            ->groups([
                Tables\Grouping\Group::make('placa')
                        ->label('Veículo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                Tables\Grouping\Group::make('planoPreventivo.descricao')
                        ->label('Plano Preventivo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
            ])
            ->defaultGroup('placa')
            ->actions([
            ])
            ;
    }

    private function getRecordUrl(Veiculo $record)
    {
        return VeiculoResource::getUrl('edit', [
            'record' => $record,
        ]);
    }

    protected function getViewData(): array
    {
        return [
            'queryCount' => $this->queryCount,
            'message' => $this->getMessage(),
            'table' => $this->getTable(),
        ];
    }

    protected function getMessage(): HtmlString
    {
        if ($this->queryCount === 0) {
            return new HtmlString(<<<HTML
            <div class="text-gray-500">
                Nenhum plano preventivo abaixo de 3.000 KM encontrado.
            </div>
            HTML
            );
        }

        return new HtmlString(<<<HTML
        <div class="text-base tracking-[0.07rem] uppercase font-normal text-red-700 dark:text-red-400">
            Planos Preventivos com vencimento abaixo de 3.000 KM ({$this->queryCount})
        </div>
        HTML
        );
    }

}
