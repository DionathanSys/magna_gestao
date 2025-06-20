<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricoMovimentoPneuResource\Pages;
use App\Filament\Resources\HistoricoMovimentoPneuResource\RelationManagers;
use App\Models\HistoricoMovimentoPneu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoricoMovimentoPneuResource extends Resource
{
    protected static ?string $model = HistoricoMovimentoPneu::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Hist. Pneus';

    protected static ?string $pluralLabel = 'Hist. Pneus';

    protected static ?string $label = 'Hist. Pneu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pneu_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('veiculo_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('data_movimento')
                    ->required(),
                Forms\Components\TextInput::make('km_inicial')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('km_final')
                    ->maxLength(255),
                Forms\Components\TextInput::make('eixo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('posicao')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sulco_movimento')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('tipo_movimento')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('motivo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('observacao')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicial')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_final')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_inicial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('km_final')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eixo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('posicao')
                    ->label('Posição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sulco_movimento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('motivo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('pneu_id')
                    ->label('Pneu')
                    ->relationship('pneu', 'numero_fogo')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable(),
            ])->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->defaultGroup('pneu.numero_fogo')
            ->groups([
                Tables\Grouping\Group::make('pneu.numero_fogo')
                    ->label('Nº de Fogo')
                    ->collapsible(),
                Tables\Grouping\Group::make('veiculo.placa')
                    ->label('Placa')
                    ->collapsible(),
            ])
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
            'index' => Pages\ListHistoricoMovimentoPneus::route('/'),
            'create' => Pages\CreateHistoricoMovimentoPneu::route('/create'),
            'edit' => Pages\EditHistoricoMovimentoPneu::route('/{record}/edit'),
        ];
    }
}
