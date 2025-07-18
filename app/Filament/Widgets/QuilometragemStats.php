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

        $dataInicial = $this->filters['dataInicial'] ?? '2025-06-10'; //now()->subMonth()->day(26);
        $dataFinal   = $this->filters['dataFinal'] ?? now();

        $dataInicial        = Carbon::parse($dataInicial);
        $dataFinal          = Carbon::parse($dataFinal);
        $placa              = $this->filters['placa'] ?? null;
        $apenasConferidas   = $this->filters['conferido'] ?? false;

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

        $km_rodado              = $viagens->sum('km_rodado');
        $km_pago                = $viagens->sum('km_pago');
        $km_dispersao           = $km_rodado - $km_pago;

        if ($km_dispersao == 0 || $km_rodado == 0) {
            $dispersao              = number_format(0, 2, ',', '.');
        } else {
            $dispersao              = number_format(($km_dispersao / $km_rodado) * 100, 2, ',', '.');
        }

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
            ->whereBetween('data_competencia', [$dataInicial->format('Y-m-d'), $dataFinal->format('Y-m-d')])
            ->count();

        $percentualConferidas = $viagens > 0 ? ($viagensConferidas / $viagens) * 100 : 0;

        return [
            Stat::make("Dispersão KM", $km_dispersao . ' - ' . $dispersao . '%')
                ->icon('heroicon-o-chart-bar')
                ->description("Km Rodado: {$km_rodado} entre {$dataInicial->format('d/m/Y')} e {$dataFinal->format('d/m/Y')}")
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
