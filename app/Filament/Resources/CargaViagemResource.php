<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargaViagemResource\Pages;
use App\Filament\Resources\CargaViagemResource\RelationManagers;
use App\Models\CargaViagem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CargaViagemResource extends Resource
{
    protected static ?string $model = CargaViagem::class;

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Cargas';

    protected static ?string $pluralLabel = 'Cargas';

    protected static ?string $label = 'Carga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('viagem_id')
                    ->relationship('viagem', 'id')
                    ->required(),
                Forms\Components\Select::make('integrado_id')
                    ->relationship('integrado', 'nome')
                    ->required(),
                Forms\Components\TextInput::make('documento_frete_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('viagem.numero_viagem')
                    ->label('Nº Viagem')
                    ->numeric(0, '', '')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('integrado.nome')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documento_frete_id')
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
            ])
            ->filters([

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
            'index' => Pages\ListCargaViagems::route('/'),
            'create' => Pages\CreateCargaViagem::route('/create'),
            // 'edit' => Pages\EditCargaViagem::route('/{record}/edit'),
        ];
    }
}
