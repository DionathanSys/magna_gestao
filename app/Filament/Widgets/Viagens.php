<?php

namespace App\Filament\Widgets;

use App\Models\Viagem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Viagens extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function getColumnSpan(): int|string|array
    {
        return 1;
    }

    public static function getSort(): int
    {
        return 2;
    }

    public function table(Table $table): Table
    {
        $viagens = Viagem::query()
            ->where('documento_transporte', null)
            ->orderBy('km_rodado_excedente', 'desc');

        return $table
            ->heading('Viagens')
            ->poll(null)
            ->description('Viagens Sem Documento de Transporte')
            ->striped()
            ->paginationPageOptions([10, 25, 50])
            ->query(
                $viagens
            )
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('numero_viagem')
                    ->label('Nº Viagem')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_competencia')
                    ->date('d/m/Y')
                    ->label('Dt. Competência'),
                Tables\Columns\TextColumn::make('data_competencia')
                    ->date('d/m/Y')
                    ->label('Dt. Competência'),
                Tables\Columns\TextColumn::make('cargas.integrado.nome')
                    ->label('Integrado')
                    ->width('1%')
                    ->listWithLineBreaks(),
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

            ]);
    }
}
