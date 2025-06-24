<?php

namespace App\Filament\Resources\ViagemResource\Widgets;

use App\Models\Viagem;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class AdvancedStatsOverviewWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected float $percentualConferido = 0;
    protected float $viagensConferidas = 0;
    protected float $totalViagens = 0;

    protected float $dispersao = 0;
    protected float $km_cobrar = 0;
    protected float $km_rodado = 0;
    protected float $km_perdido = 0;
    protected float $km_rodado_excedente = 0;
    protected float $km_pago_excedente = 0;

    public function __construct()
    {
        $this->totalViagens = Viagem::count();
        $this->viagensConferidas = Viagem::query()->where('conferido', true)->count();
        $this->percentualConferido = (Viagem::query()->where('conferido', true)->count() / Viagem::count()) * 100;

        $this->km_rodado = Viagem::sum('km_rodado');
        $this->km_rodado_excedente = Viagem::sum('km_rodado_excedente');
        $this->km_pago_excedente = Viagem::sum('km_pago_excedente');
        $this->km_perdido = $this->km_rodado_excedente - $this->km_pago_excedente;
        $this->km_cobrar = Viagem::sum('km_cobrar');
        $this->dispersao = number_format((($this->km_perdido / $this->km_rodado) * 100), 2);
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Viagens Conferidas / Total Viagens', $this->viagensConferidas . ' / ' . $this->totalViagens)
                ->icon('heroicon-o-document-chart-bar')
                ->backgroundColor('gray')
                ->progress($this->percentualConferido)
                ->progressBarColor('success')
                ->chartColor('success')
                ->description('% Viagens Conferidas')
                ->descriptionColor('info'),
            Stat::make('% Dispersão', number_format($this->dispersao, 2, ',', '.') . '%')
                ->icon('heroicon-o-newspaper')
                ->description(number_format($this->km_perdido, 2, ',', '.') . ' / ' . number_format($this->km_rodado, 2, ',', '.'))
                ->iconColor('warning'),
            Stat::make('Km Cobrar', number_format($this->km_cobrar, 0, ',', '.'))
                ->iconColor('success'),

        ];
    }
}
