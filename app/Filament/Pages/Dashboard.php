<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Log;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    public function getColumns(): int | string | array
    {
        return 12;
    }

    protected function getHeaderActions(): array
    {

        return [
            FilterAction::make()
                ->form([
                    Select::make('placa')
                            ->label('Placa')
                            ->options(fn () => \App\Models\Veiculo::all()->pluck('placa', 'id'))
                            ->searchable()
                            ->placeholder('Selecione uma placa'),
                    DatePicker::make('dataInicial')
                        ->maxDate(fn (Get $get) => $get('dataFinal') ?: now())
                        ->reactive(),
                    DatePicker::make('dataFinal')
                        ->maxDate(now())
                        ->reactive(),
                    Checkbox::make('conferido')
                        ->label('Apenas Viagens Conferidas'),
                ]),
        ];
    }

}
