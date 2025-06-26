<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Log;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function getColumns(): int | string | array
    {
        return 12;
    }

    public function filtersForm(Form $form): Form
    {

        Log::debug('Dashboard filtersForm method called');

        $dataInicial = now()->day < 26 ? now()->subMonth()->day(26) : now()->day(26);
        $dataFinal = now();

        $dataInicial = $dataInicial->format('Y-m-d');
        $dataFinal = $dataFinal->format('Y-m-d');

        Log::debug('Data Inicial: ' . $dataInicial);
        Log::debug('Data Final: ' . $dataFinal);
        
        return $form
            ->schema([
                Section::make()
                    ->label('Filtros')
                    ->description('Selecione os filtros desejados para a pesquisa')
                    ->columns(6)
                    ->schema([
                        Select::make('placa')
                            ->label('Placa')
                            ->options(fn () => \App\Models\Veiculo::all()->pluck('placa', 'id'))
                            ->searchable()
                            ->placeholder('Selecione uma placa'),
                        DatePicker::make('data_incial')
                            // ->maxDate(fn (Get $get) => $get('data_incial') ?: now())
                            ->default($dataInicial),
                        DatePicker::make('data_final')
                            // ->minDate(fn (Get $get) => $get('data_final') ?: now())
                            ->maxDate(now())
                            ->default($dataFinal),
                    ]),
            ]);
    }
}
