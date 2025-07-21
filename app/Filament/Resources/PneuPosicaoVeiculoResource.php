<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PneuPosicaoVeiculoResource\Pages;
use App\Filament\Resources\PneuPosicaoVeiculoResource\RelationManagers;
use App\Models\PneuPosicaoVeiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PneuPosicaoVeiculoResource extends Resource
{
    protected static ?string $model = PneuPosicaoVeiculo::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Posições de Pneus';

    protected static ?string $pluralLabel = 'Posições de Pneus';

    protected static ?string $label = 'Posição de Pneu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                    $query->with(['veiculo', 'veiculo.kmAtual', 'pneu'])
                    ->whereHas('pneu');
                })
            ->columns([
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicial')
                    ->label('Dt. Inicial')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_inicial')
                    ->label('Km. Rodado')
                    ->width('1%')
                    ->state(fn (PneuPosicaoVeiculo $record): string => $record->km_inicial ? (($record->veiculo->kmAtual->quilometragem ?? 0) - $record->km_inicial) : 'N/A')
                    ->numeric(0, ',', '.'),
                Tables\Columns\TextColumn::make('veiculo.kmAtual.quilometragem')
                    ->width('1%')
                    ->numeric(0, ',', '.'),
                Tables\Columns\TextColumn::make('eixo')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('posicao')
                    ->label('Posição')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Atualizado por')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('pneu_id')
                    ->label('Pneu')
                    ->relationship('pneu', 'numero_fogo')
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('eixo')
                    ->label('Eixo')
                    ->collapsible(),
                Tables\Grouping\Group::make('veiculo.placa')
                    ->label('Veículo')
                    ->collapsible(),
            ])
            ->defaultsort('km_inicial', 'desc')
            ->defaultGroup('eixo')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListPneuPosicaoVeiculos::route('/'),
            'edit' => Pages\EditPneuPosicaoVeiculo::route('/{record}/edit'),
        ];
    }
}
