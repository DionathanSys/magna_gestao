<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoPreventivoResource\Pages;
use App\Filament\Resources\PlanoPreventivoResource\RelationManagers;
use App\Models\PlanoPreventivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoPreventivoResource extends Resource
{
    protected static ?string $model = PlanoPreventivo::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Planos Preventivos';

    protected static ?string $pluralLabel = 'Planos Preventivos';

    protected static ?string $label = 'Plano Preventivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Forms\Components\TextInput::make('descricao')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('periodicidade')
                    ->label('Periodicidade')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('intervalo')
                    ->label('Intervalo')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->required()
                    ->default(true),
                Forms\Components\KeyValue::make('itens')
                    ->label('Itens do Plano')
                    ->keyLabel('Item')
                    ->valueLabel('Descrição')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('periodicidade')
                    ->label('Periodicidade'),
                Tables\Columns\TextColumn::make('intervalo')
                    ->label('Intervalo'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo'),
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
            'index' => Pages\ListPlanoPreventivos::route('/'),
            'create' => Pages\CreatePlanoPreventivo::route('/create'),
            'edit' => Pages\EditPlanoPreventivo::route('/{record}/edit'),
        ];
    }
}
