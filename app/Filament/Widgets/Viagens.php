<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ViagemResource;
use App\Models\Viagem;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Viagens extends BaseWidget
{

    // protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function getColumnSpan(): int|string|array
    {
        return 6;
    }

    public static function getSort(): int
    {
        return 2;
    }

    public function table(Table $table): Table
    {
        $viagens = Viagem::query()
            ->orderBy('km_rodado_excedente', 'desc');

        return $table
            ->heading('Viagens')
            ->poll(null)
            ->description('Viagens Sem Documento de Transporte - Total: ' . $viagens->count())
            ->striped()
            ->paginationPageOptions([10, 25, 50])
            ->query(
                $viagens
            )
            ->groups(
                [
                    Tables\Grouping\Group::make('data_competencia')
                        ->label('Data Competência')
                        ->titlePrefixedWithLabel(false)
                        ->getTitleFromRecordUsing(fn (Viagem $record): string => Carbon::parse($record->data_competencia)->format('d/m/Y'))
                        ->collapsible(),
                    Tables\Grouping\Group::make('veiculo.placa')
                        ->label('Veículo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),

                ]
            )
            ->searchOnBlur()
            ->groupsOnly()
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('km_rodado')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                Tables\Columns\TextColumn::make('km_pago')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                Tables\Columns\TextColumn::make('km_rodado_excedente')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                Tables\Columns\TextColumn::make('km_pago_excedente')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                Tables\Columns\TextColumn::make('km_cobrar')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),

            ]);
    }
}
