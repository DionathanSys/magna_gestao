<?php

namespace App\Filament\Widgets;

use App\Models\DocumentoFrete;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class FaturamentoStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('R$', number_format(DocumentoFrete::sum('valor_total'), 2, ',', '.'))
                ->icon('heroicon-o-document-chart-bar')
                ->backgroundColor('gray')

                ->progressBarColor('success')
                ->chartColor('success')
                ->description('% Viagens Conferidas')
                ->descriptionColor('info'),
        ];
    }
}
