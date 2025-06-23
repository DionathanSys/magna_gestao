<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Section::make()
                    ->label('Filtros')
                    ->columns(12)
                    ->schema([
                        Select::make('businessCustomersOnly')
                            ->columnSpan(2)
                            ->boolean(),
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                                ->columnSpan(2),
                        DatePicker::make('endDate')
                            ->columnSpan(2)
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(3),
            ]);
    }
}
