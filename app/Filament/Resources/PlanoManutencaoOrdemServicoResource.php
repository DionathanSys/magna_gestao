<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoManutencaoOrdemServicoResource\Pages;
use App\Filament\Resources\PlanoManutencaoOrdemServicoResource\RelationManagers;
use App\Models\PlanoManutencaoOrdemServico;
use App\Models\PlanoManutencaoVeiculo;
use App\Models\PlanoPreventivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoManutencaoOrdemServicoResource extends Resource
{
    protected static ?string $model = PlanoManutencaoOrdemServico::class;

    protected static ?string $navigationGroup = 'Manutenção';

    protected static ?string $pluralModelLabel = 'Hist. Planos Preventivos';

    protected static ?string $pluralLabel = 'Hist. Planos Preventivos';

    protected static ?string $label = 'Hist. Planos Preventivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->live()
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('plano_preventivo_id')
                    ->label('Plano Preventivo')
                    ->options(function (Forms\Get $get) {
                        return PlanoPreventivo::query()
                            ->join('planos_manutencao_veiculo', 'planos_manutencao_veiculo.plano_preventivo_id', '=', 'planos_preventivo.id')
                            ->where('planos_manutencao_veiculo.veiculo_id', $get('veiculo_id'))
                            ->where('planos_preventivo.is_active', true)
                            ->orderBy('planos_preventivo.descricao')
                            ->pluck('planos_preventivo.descricao', 'planos_preventivo.id');
                    })
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('km_execucao')
                    ->label('KM de Execução')
                    ->columnStart(1)
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('data_execucao')
                    ->label('Data de Execução')
                    ->required(),
                Forms\Components\TextInput::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('plano_preventivo_id')
                    ->label('Plano Preventivo')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planoPreventivo.descricao')
                    ->label('Descrição')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->width('1%')
                    ->placeholder('Sem Vínculo')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Veículo')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('km_execucao')
                    ->label('KM de Execução')
                    ->width('1%')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_execucao')
                    ->label('Data de Execução')
                    ->date('d/m/Y')
                    ->sortable(),
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
            ])
            ->filters([
                //
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
            ->poll('5s')
            ->persistFiltersInSession();
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
            'index' => Pages\ListPlanoManutencaoOrdemServicos::route('/'),
            // 'create' => Pages\CreatePlanoManutencaoOrdemServico::route('/create'),
            // 'edit' => Pages\EditPlanoManutencaoOrdemServico::route('/{record}/edit'),
        ];
    }
}
