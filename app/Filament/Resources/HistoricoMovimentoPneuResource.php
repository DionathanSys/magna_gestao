<?php

namespace App\Filament\Resources;

use App\Enum\Pneu\MotivoMovimentoPneuEnum;
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
                Forms\Components\DatePicker::make('data_inicial')
                    ->required(),
                Forms\Components\DatePicker::make('data_final')
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
                    ->label('Posição')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sulco_movimento')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Select::make('motivo')
                    ->options(MotivoMovimentoPneuEnum::toSelectArray()),
                Forms\Components\TextInput::make('ciclo_vida')
                    ->label('Vida')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(3),
                Forms\Components\TextInput::make('observacao')
                    ->label('Observação')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->label('Nº de Fogo')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicial')
                    ->width('1%')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_final')
                    ->width('1%')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_inicial')
                    ->width('1%')
                    ->numeric(null, '', '.')
                    ->searchable(),
                Tables\Columns\TextColumn::make('km_final')
                    ->width('1%')
                    ->numeric(null, '', '.')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ciclo_vida')
                    ->label('Vida Pneu')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('eixo')
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('posicao')
                    ->width('1%')
                    ->label('Posição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sulco_movimento')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('motivo')
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observacao')
                    ->width('1%')
                    ->label('Observação')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
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
            // 'create' => Pages\CreateHistoricoMovimentoPneu::route('/create'),
            // 'edit' => Pages\EditHistoricoMovimentoPneu::route('/{record}/edit'),
        ];
    }
}
