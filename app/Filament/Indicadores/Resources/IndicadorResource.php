<?php

namespace App\Filament\Indicadores\Resources;

use App\Filament\Indicadores\Resources\IndicadorResource\Pages;
use App\Filament\Indicadores\Resources\IndicadorResource\RelationManagers;
use App\Models\Indicador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IndicadorResource extends Resource
{
    protected static ?string $model = Indicador::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descricao')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('objetivo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('peso')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('periodicidade')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tipo')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descricao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('objetivo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peso')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodicidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(),
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
            'index' => Pages\ListIndicadors::route('/'),
            'create' => Pages\CreateIndicador::route('/create'),
            'edit' => Pages\EditIndicador::route('/{record}/edit'),
        ];
    }
}
