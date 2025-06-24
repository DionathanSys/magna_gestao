<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Log;

class QuilometragemStats extends BaseWidget
{

    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $dataInicial = $this->filters['data_inicial'] ?? now()->subMonth()->day(26);
        $dataFinal   = $this->filters['data_final'] ?? now();
        $placa       = $this->filters['placa'];

        Log::debug('QuilometragemStats', [
            'data_inicial' => $dataInicial,
            'data_final'   => $dataFinal,
            'placa'        => $placa,
        ]);

        $viagens = \App\Models\Viagem::query()
            ->when($placa, function ($query) use ($placa) {
                $query->whereHas('veiculo', function ($q) use ($placa) {
                    $q->where('id', $placa);
                });
            })
            ->whereBetween('data_inicio', [$dataInicial, $dataFinal]);

        $viagensConferidas = \App\Models\Viagem::query()->where('conferido', true)->get();
        $percentualConferidas = $viagens->count() > 0 ? ($viagensConferidas->count() / $viagens->count()) * 100 : 0;

        $km_rodado = $viagens->sum('km_rodado');
        $km_rodado_excedente = $viagens->sum('km_rodado_excedente');
        $km_dispersao = ($km_rodado_excedente / $km_rodado);

        $km_rodado = number_format($km_rodado, 2, ',', '.');
        $km_rodado_excedente = number_format($km_rodado_excedente, 2, ',', '.');
        $dispersao = number_format($km_dispersao, 2, ',', '.');

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
