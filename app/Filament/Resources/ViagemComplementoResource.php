<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViagemComplementoResource\Pages;
use App\Filament\Resources\ViagemComplementoResource\RelationManagers;
use App\Models\ViagemComplemento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ViagemComplementoResource extends Resource
{
    protected static ?string $model = ViagemComplemento::class;

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Complementos de Viagem';

    protected static ?string $pluralLabel = 'Complementos de Viagem';

    protected static ?string $label = 'Complemento de Viagem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('viagem_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('numero_viagem')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('documento_transporte')
                    ->maxLength(255),
                Forms\Components\TextInput::make('integrado_id')
                    ->numeric(),
                Forms\Components\TextInput::make('km_rodado')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('km_pago')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('km_divergencia')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('km_cobrar')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('motivo_divergencia')
                    ->maxLength(255),
                Forms\Components\TextInput::make('data_competencia')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('conferido')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('viagem_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_viagem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->searchable(),
                Tables\Columns\TextColumn::make('integrado_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_rodado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_pago')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_divergencia')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_cobrar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('motivo_divergencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_competencia')
                    ->searchable(),
                Tables\Columns\IconColumn::make('conferido')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
            'index' => Pages\ListViagemComplementos::route('/'),
            'create' => Pages\CreateViagemComplemento::route('/create'),
            'edit' => Pages\EditViagemComplemento::route('/{record}/edit'),
        ];
    }
}
