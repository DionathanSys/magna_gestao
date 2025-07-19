<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoManutencaoVeiculoResource\Pages;
use App\Filament\Resources\PlanoManutencaoVeiculoResource\RelationManagers;
use App\Filament\Resources\PlanoPreventivoResource\RelationManagers\VeiculosRelationManager;
use App\Models\PlanoManutencaoVeiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoManutencaoVeiculoResource extends Resource
{
    protected static ?string $model = PlanoManutencaoVeiculo::class;

     protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Planos Preventivos Veículos';

    protected static ?string $pluralLabel = 'Planos Preventivos Veículos';

    protected static ?string $label = 'Plano Preventivo Veículo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plano_preventivo_id')
                    ->label('Plano Preventivo')
                    ->relationship('planoPreventivo', 'descricao')
                    ->required(),
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('planoPreventivo.descricao')
                    ->label('Plano Preventivo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Veículo')
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
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanoManutencaoVeiculos::route('/'),
            // 'create' => Pages\CreatePlanoManutencaoVeiculo::route('/create'),
            'edit' => Pages\EditPlanoManutencaoVeiculo::route('/{record}/edit'),
        ];
    }
}
