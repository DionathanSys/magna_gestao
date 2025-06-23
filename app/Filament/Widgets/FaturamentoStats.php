<?php

namespace App\Filament\Widgets;

use App\Models\DocumentoFrete;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class FaturamentoStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $primeiraData = DocumentoFrete::query()
            ->orderBy('data_emissao', 'asc')
            ->first()
            ->data_emissao ?? now();

        $primeiraData = Carbon::parse($primeiraData)->format('d/m/Y');

        $ultimaData = DocumentoFrete::query()
            ->orderBy('data_emissao', 'desc')
            ->first()
            ->data_emissao ?? now();

        $ultimaData = Carbon::parse($ultimaData)->format('d/m/Y');

        return [
            Stat::make('Faturamento', "R$ " . number_format(DocumentoFrete::sum('valor_total'), 2, ',', '.'))
                ->icon('heroicon-o-document-chart-bar')
                ->backgroundColor('gray')

                ->progressBarColor('success')
                ->chartColor('success')
                ->description("Valor Total Faturado de {$primeiraData} até {$ultimaData}")
                ->descriptionColor('info'),
        ];
    }
}
