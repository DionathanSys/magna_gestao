<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Log;

class QuilometragemStats extends BaseWidget
{

    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        Log::debug("QuilometragemStats getStats method called");

        $dataInicial = Carbon::parse($this->filters['data_inicial'] ?? now()->subMonth()->day(26))->format('Y-m-d');
        $dataFinal   = Carbon::parse($this->filters['data_final'] ?? now())->format('Y-m-d');
        $placa       = $this->filters['placa'];

        Log::debug('QuilometragemStats', [
            'data_inicial' => $dataInicial,
            'data_final'   => $dataFinal,
            'placa'        => $placa,
        ]);

        $viagens = \App\Models\Viagem::query()
            ->select('id')
            ->when($placa, function ($query) use ($placa) {
                $query->whereHas('veiculo', function ($q) use ($placa) {
                    $q->where('id', $placa);
                });
            })
            ->whereBetween('data_competencia', [$dataInicial, $dataFinal]);

        $viagensConferidas = \App\Models\Viagem::query()
            ->where('conferido', true)
            ->whereBetween('data_competencia', [$dataInicial, $dataFinal]);

        Log::debug('Viagens Query', [
            'sql' => $viagens->dump(),
            'bindings' => $viagens->getBindings(),
        ]);

        Log::debug('Viagens Conferidas Query', [
            'sql' => $viagensConferidas->dump(),
            'bindings' => $viagensConferidas->getBindings(),
        ]);

        $viagensConferidas      = $viagensConferidas->count();
        $viagens                = $viagens->count();

        $percentualConferidas = $viagens->count() > 0 ? ($viagensConferidas / $viagens) * 100 : 0;

        Log::debug('Percentual Conferidas', [
            'total'         => $viagens,
            'conferidas'    => $viagensConferidas,
            'percentual'    => $percentualConferidas,
        ]);

        $km_rodado              = $viagens->sum('km_rodado');
        $km_rodado_excedente    = $viagens->sum('km_rodado_excedente');
        $km_dispersao           = ($km_rodado_excedente / $km_rodado);

        Log::debug('Km Rodado', [
            'km_rodado'           => $km_rodado,
            'km_rodado_excedente' => $km_rodado_excedente,
            'km_dispersao'        => $km_dispersao,
        ]);
        
        $km_rodado              = number_format($km_rodado, 2, ',', '.');
        $km_rodado_excedente    = number_format($km_rodado_excedente, 2, ',', '.');
        $dispersao              = number_format($km_dispersao, 2, ',', '.');


        return [
            Stat::make("Km Perdida", $km_rodado_excedente . ' - ' . $dispersao . '%')
                ->icon('heroicon-o-chart-bar')
                ->description("Km Rodado: {$km_rodado}")
                ->descriptionIcon('heroicon-o-information-circle', 'before')
                ->descriptionColor('primary')
                ->iconColor('warning'),
            Stat::make("Conferência Viagens", number_format($percentualConferidas, 2, ',', '.') . '%')
                ->icon('heroicon-o-newspaper')
                ->description("Viagens Conferidas: {$viagensConferidas->count()}/{$viagens->count()}")
                ->descriptionIcon('heroicon-o-information-circle', 'before')
                ->descriptionColor('primary')
                ->iconColor('warning')
                ->progress($percentualConferidas)
                ->progressBarColor('info')
                ->chartColor('info'),

        ];
    }
}
