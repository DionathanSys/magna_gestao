<?php

namespace App\Filament\Widgets;

use App\Models\Veiculo;
use App\Models\Viagem;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ChartTest extends ApexChartWidget
{

    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;

    public function getColumnSpan(): int | string | array
    {
        return 12;
    }


    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'chartTest';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Km dispersão por veículo';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $dados = $this->getKmDispersaoPorVeiculo();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'ChartTest',
                    'data' => $dados->pluck('km_dispersao')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $dados->pluck('placa')->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
        ];
    }

    public function getSubheading(): string
    {
        $dataInicial = $this->filters['dataInicial'] ?? now()->subMonth()->day(26);
        $dataFinal   = $this->filters['dataFinal'] ?? now();

        $dataInicial = Carbon::parse($dataInicial)->format('d/m/Y');
        $dataFinal   = Carbon::parse($dataFinal)->format('d/m/Y');

        return "Período: {$dataInicial} até {$dataFinal}";
    }

    public function getKmDispersaoPorVeiculo()
    {

        $dataInicial = $this->filters['dataInicial'] ?? now()->subMonth()->day(26);
        $dataFinal   = $this->filters['dataFinal'] ?? now();

        $dataInicial = Carbon::parse($dataInicial);
        $dataFinal   = Carbon::parse($dataFinal);

        return \App\Models\Viagem::query()
            ->select('veiculo_id', DB::raw('SUM(km_rodado - km_pago) as km_dispersao'))
            ->with('veiculo:id,placa')
            ->whereBetween('data_competencia', [$dataInicial->format('Y-m-d'), $dataFinal->format('Y-m-d')])
            ->groupBy('veiculo_id')
            ->get()
            ->map(function ($item) {
                return [
                    'veiculo_id' => $item->veiculo_id,
                    'placa'      => $item->veiculo?->placa,
                    'km_dispersao'  => $item->km_dispersao,
                ];
            });
    }
}
