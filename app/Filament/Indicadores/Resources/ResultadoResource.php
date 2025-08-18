<?php

namespace App\Filament\Indicadores\Resources;

use App\Filament\Indicadores\Resources\ResultadoResource\Pages;
use App\Filament\Indicadores\Resources\ResultadoResource\RelationManagers;
use App\Models\Resultado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Leandrocfe\FilamentPtbrFormFields\Money;

class ResultadoResource extends Resource
{
    protected static ?string $model = Resultado::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                static::getGestorIdFormField(),
                static::getIndicadorIdFormField(),
                static::getStatusFormField(),
                static::getPontuacaoObtidaFormField(),
                static::getPontuacaoMaximaFormField(),
                static::getPeriodoFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gestor.nome')
                    ->label('Gestor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.descricao')
                    ->label('Indicador')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pontuacao_obtida')
                    ->label('Pontuação Obtida')
                    ->numeric('2', ',' , '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pontuacao_maxima')
                    ->label('Pontuação Máxima')
                    ->state(fn($record) => ($record->pontuacao_maxima > 0) ? ($record->pontuacao_obtida / $record->pontuacao_maxima) * 100 : 0 )
                    ->suffix('%')
                    ->numeric('2', ',' , '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResultados::route('/'),
            'create' => Pages\CreateResultado::route('/create'),
            'edit' => Pages\EditResultado::route('/{record}/edit'),
        ];
    }

    public static function getIndicadorIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('indicador_id')
            ->label('Indicador')
            ->columnSpan(4)
            ->relationship('indicador', 'descricao')
            ->required()
            ->preload()
            ->searchable();
    }

    public static function getGestorIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('gestor_id')
            ->label('Gestor')
            ->columnSpan(4)
            ->relationship('gestor', 'nome')
            ->preload()
            ->searchable();
    }

    public static function getPontuacaoObtidaFormField(): Money
    {
        return Money::make('pontuacao_obtida')
            ->label('Pontuação Obtida')
            ->columnSpan(2)
            ->prefix(null)
            ->required()
            ->minValue(0);
    }

    public static function getPontuacaoMaximaFormField(): Money
    {
        return Money::make('pontuacao_maxima')
            ->label('Pontuação Máxima')
            ->columnSpan(2)
            ->prefix(null)
            ->required()
            ->minValue(0);
    }

    public static function getStatusFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status')
            ->label('Status')
            ->columnSpan(3)
            ->options([
                'n_atingido' => 'Não Atingido',
                'parcialmente_atingido' => 'Parcialmente Atingido',
                'atingido' => 'Atingido',
            ])
            ->default('n_atingido')
            ->required();
    }
    public static function getPeriodoFormField(): Forms\Components\DatePicker
    {
        return Forms\Components\DatePicker::make('periodo')
            ->label('Período')
            ->columnSpan(2)
            ->required();
    }

}
