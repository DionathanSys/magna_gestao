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
        // ds($dados);
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'KM Perdido',
                    'data' => $dados->pluck('km_dispersao')->toArray(),
                    'type' => 'column',
                ],
                [
                    'name' => '% Dispersão',
                    'data' => $dados->pluck('dispersao')->toArray(),
                    'type' => 'line',
                ],
            ],
            'stroke' => [
                'width' => [0, 4],
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
            [
                'title' => ['text' => 'KM'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            [
                'opposite' => true,
                'title' => ['text' => '% Dispersão'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'min' => 0,
                'max' => 10,
            ],
        ],
        'colors' => ['#f59e0b', '#3b82f6', '#ef4444'],
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
            ->select('veiculo_id', DB::raw('SUM(km_rodado - km_pago) as km_dispersao'), DB::raw('SUM(km_rodado) as km_rodado'))
            ->with('veiculo:id,placa')
            ->whereBetween('data_competencia', [$dataInicial->format('Y-m-d'), $dataFinal->format('Y-m-d')])
            ->groupBy('veiculo_id')
            ->get()
            ->map(function ($item) {
                return [
                    'veiculo_id'    => $item->veiculo_id,
                    'placa'         => $item->veiculo?->placa,
                    'km_dispersao'  => number_format($item->km_dispersao, 2, ',', '.'),
                    'km_rodado'     => number_format($item->km_rodado, 2, ',', '.'),
                    'dispersao'     => $item->km_rodado > 0 ? number_format(($item->km_dispersao / $item->km_rodado) * 100, 2, ',', '.') : '0,00',
                ];
            });
    }
}
