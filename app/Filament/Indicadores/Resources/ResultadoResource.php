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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                static::getGestorIdFormField(),
                static::getIndicadorIdFormField(),
                static::getPontuacaoFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gestor.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pontuacao')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
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
            ->relationship('indicador', 'id')
            ->required();
    }

    public static function getGestorIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('gestor_id')
            ->relationship('gestor', 'id')
            ->required();
    }

    public static function getPontuacaoFormField(): Money
    {
        return Money::make('pontuacao')
            ->label('Pontuação')
            ->required()
            ->numeric()
            ->minValue(0);
    }

}
