<?php

namespace App\Filament\Widgets;

use App\Models\Veiculo;
use App\Models\Viagem;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ChartTest extends ApexChartWidget
{

    use InteractsWithPageFilters;

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
        $dados = self::getKmDispersaoPorVeiculo();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'ChartTest',
                    'data' => $dados->pluck('dispersao')->toArray(),
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

    public static function getKmDispersaoPorVeiculo()
    {
        $dataInicial = (new self)->filters['dataInicial'] ?? now()->subMonth()->day(26);
        $dataFinal   = (new self)->filters['dataFinal'] ?? now();

        $dataInicial = Carbon::parse($dataInicial)->format('Y-m-d');
        $dataFinal   = Carbon::parse($dataFinal)->format('Y-m-d');

        return \App\Models\Viagem::query()
            ->select('veiculo_id', DB::raw('SUM(km_rodado - km_pago) as dispersao'))
            ->with('veiculo:id,placa')
            ->whereBetween('data_competencia', [$dataInicial, $dataFinal])
            ->groupBy('veiculo_id')
            ->get()
            ->map(function ($item) {
                return [
                    'veiculo_id' => $item->veiculo_id,
                    'placa'      => $item->veiculo?->placa,
                    'dispersao'  => $item->dispersao,
                ];
            });
    }
}
