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
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoManutencaoVeiculoResource extends Resource
{
    protected static ?string $model = PlanoManutencaoVeiculo::class;

    protected static ?string $navigationGroup = 'Manutenção';

    protected static ?string $pluralModelLabel = 'Planos Preventivos - Veículos';

    protected static ?string $pluralLabel = 'Planos Preventivos - Veículos';

    protected static ?string $label = 'Plano Preventivo - Veículo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plano_preventivo_id')
                    ->label('Plano Preventivo')
                    ->relationship('planoPreventivo', 'descricao')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('planoPreventivo.descricao')
                    ->label('Plano Preventivo')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Veículo')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('veiculo.kmAtual.quilometragem')
                    ->label('Quilometragem Atual')
                    ->width('1%')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quilometragem_restante')
                    ->label('Quilometragem Restante')
                    ->width('1%')
                    ->badge()
                    ->color(fn ($record) => $record->quilometragem_restante < 3001 ? 'danger' : 'info')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('proxima_execucao')
                    ->label('Próxima Execução')
                    ->width('1%')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ultima_execucao.km_execucao')
                    ->label('KM de Execução')
                    ->width('1%')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ultima_execucao.data_execucao')
                    ->label('Data de Execução')
                    ->width('1%')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->groups([
                Tables\Grouping\Group::make('planoPreventivo.descricao')
                    ->label('Plano Preventivo')
                    ->collapsible(),
                Tables\Grouping\Group::make('veiculo.placa')
                    ->label('Veículo')
                    ->collapsible(),
            ])
            ->defaultGroup('veiculo.placa')
            ->defaultSort('planoPreventivo.descricao', 'asc')
            ->deferFilters()
            ->searchOnBlur()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('5s');
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
