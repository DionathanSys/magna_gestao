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

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        ds('QuilometragemStats getStats method called');

        ds($this->filters)->label('Filters');

        $dataInicial = $this->filters['dataInicial'] ?? now()->subMonth()->day(26);
        $dataFinal   = $this->filters['dataFinal'] ?? now();

        $dataInicial        = Carbon::parse($dataInicial)->format('Y-m-d');
        $dataFinal          = Carbon::parse($dataFinal)->format('Y-m-d');
        $placa              = $this->filters['placa'] ?? null;
        $apenasConferidas   = $this->filters['conferido'] ?? false;

        ds([
            'data_inicial' => $dataInicial,
            'data_final'   => $dataFinal,
            'placa'        => $placa,
        ])->label('Filter Values');

        $viagens = \App\Models\Viagem::query()
            ->when($placa, function ($query) use ($placa) {
                $query->whereHas('veiculo', function ($q) use ($placa) {
                    $q->where('id', $placa);
                });
            })
            ->when($apenasConferidas, function ($query) {
                $query->where('conferido', true);
            })
            ->whereBetween('data_competencia', [$dataInicial, $dataFinal]);

        ds([
            'data_inicial' => $dataInicial,
            'data_final' => $dataFinal,
        ])->label('Datas Utilizadas');

        $km_rodado              = $viagens->sum('km_rodado');
        $km_pago                = $viagens->sum('km_pago');
        $km_dispersao           = $km_rodado - $km_pago;
        $dispersao              = number_format(($km_dispersao / $km_rodado) * 100, 2, ',', '.');

        $km_dispersao           = number_format($km_dispersao, 2, ',', '.');
        $km_rodado              = number_format($viagens->sum('km_rodado'), 2, ',', '.');
        $km_pago                = number_format($viagens->sum('km_pago'), 2, ',', '.');

        $viagens                = $viagens->count();

        $viagensConferidas = \App\Models\Viagem::query()
            ->when($placa, function ($query) use ($placa) {
                $query->whereHas('veiculo', function ($q) use ($placa) {
                    $q->where('id', $placa);
                });
            })
            ->where('conferido', true)
            ->whereBetween('data_competencia', [$dataInicial, $dataFinal])
            ->count();

        $percentualConferidas = $viagens > 0 ? ($viagensConferidas / $viagens) * 100 : 0;

        return [
            Stat::make("Dispersão KM", $km_dispersao . ' - ' . $dispersao . '%')
                ->icon('heroicon-o-chart-bar')
                ->description("Km Rodado: {$km_rodado}")
                ->descriptionIcon('heroicon-o-information-circle', 'before')
                ->descriptionColor('primary')
                ->iconColor('warning'),
            Stat::make("Conferência Viagens", number_format($percentualConferidas, 2, ',', '.') . '%')
                ->icon('heroicon-o-newspaper')
                ->description("Viagens Conferidas: {$viagensConferidas}/{$viagens}")
                ->descriptionIcon('heroicon-o-information-circle', 'before')
                ->descriptionColor('primary')
                ->iconColor('warning')
                ->progress($percentualConferidas)
                ->progressBarColor('info')
                ->chartColor('info'),

        ];
    }
}
