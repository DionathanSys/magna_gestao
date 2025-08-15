<?php

namespace App\Filament\Indicadores\Resources;

use App\Filament\Indicadores\Resources\GestorResource\Pages;
use App\Filament\Indicadores\Resources\GestorResource\RelationManagers;
use App\Models\Gestor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GestorResource extends Resource
{
    protected static ?string $model = Gestor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListGestors::route('/'),
            'create' => Pages\CreateGestor::route('/create'),
            'edit' => Pages\EditGestor::route('/{record}/edit'),
        ];
    }

    public static function getNomeFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('nome')
            ->label('Nome')
            ->required()
            ->maxLength(255);
    }

    public static function getUnidadeFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('unidade')
            ->label('Unidade')
            ->options([
                'CATANDUVAS' => 'CATANDUVAS',
                'CHAPECÓ'   => 'CHAPECÓ',
                'CONCÓRDIA' => 'CONCÓRDIA'
            ])
            ->required();
    }

    public static function getSetorFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('setor')
            ->label('Setor')
            ->required();
    }


}
