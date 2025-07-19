<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoManutencaoOrdemServicoResource\Pages;
use App\Filament\Resources\PlanoManutencaoOrdemServicoResource\RelationManagers;
use App\Models\PlanoManutencaoOrdemServico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoManutencaoOrdemServicoResource extends Resource
{
    protected static ?string $model = PlanoManutencaoOrdemServico::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Plano Preventivo - Ordem de Serviço';

    protected static ?string $pluralLabel = 'Plano Preventivo - Ordem de Serviço';

    protected static ?string $label = 'Plano Preventivo - Ordem de Serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('veiculo_id')
                    ->relationship('veiculo', 'placa')
                    ->live()
                    ->required(),
                Forms\Components\Select::make('plano_preventivo_id')
                    ->relationship('planoPreventivoVinculado', 'id', function ($query, Forms\Get $get) {
                        return $query
                            ->where('is_active', true)
                            ->where('veiculo_id', $get('veiculo_id'))
                            ->orderBy('placa');
                    })
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('ordem_servico_id')
                    ->numeric(),
                Forms\Components\TextInput::make('km_execucao')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('data_execucao')
                    ->label('Data de Execução')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plano_preventivo_id')
                    ->label('Plano Preventivo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Veículo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_execucao')
                    ->label('KM de Execução')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_execucao')
                    ->label('Data de Execução')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
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
            'index' => Pages\ListPlanoManutencaoOrdemServicos::route('/'),
            // 'create' => Pages\CreatePlanoManutencaoOrdemServico::route('/create'),
            // 'edit' => Pages\EditPlanoManutencaoOrdemServico::route('/{record}/edit'),
        ];
    }
}
