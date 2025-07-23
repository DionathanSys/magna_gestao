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
                Forms\Components\Select::make('pneu_id')
                    ->label('Nº de Fogo')
                    ->relationship('pneu', 'numero_fogo')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('data_inicial')
                    ->label('Data Inicial')
                    ->required(),
                Forms\Components\DatePicker::make('data_final')
                    ->label('Data Final')
                    ->required(),
                Forms\Components\TextInput::make('km_inicial')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('km_final')
                    ->numeric(),
                Forms\Components\TextInput::make('eixo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('posicao')
                    ->label('Posição')
                    ->required()
                    ->minLength(2)
                    ->maxLength(4),
                Forms\Components\TextInput::make('sulco_movimento')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Select::make('motivo')
                    ->required()
                    ->options(MotivoMovimentoPneuEnum::toSelectArray()),
                Forms\Components\TextInput::make('ciclo_vida')
                    ->label('Vida')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(3),
                Forms\Components\RichEditor::make('observacao')
                    ->label('Observação')
                    ->columnSpanFull()
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
                Tables\Columns\TextColumn::make('kmPercorrido')
                    ->width('1%')
                    ->numeric(null, '', '.')
                    ->summarize(
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total KM Percorrido')
                            ->using(function ($query) {
                                // Busca todos os registros filtrados
                                $total = $query->get()->sum(fn($item) => $item->km_final - $item->km_inicial);
                                return number_format($total, 2, ',', '.');
                            })
                    ),
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
                Tables\Actions\CreateAction::make()
                    // ->successNotification(null),
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
                    // ->successNotification(null)
                    ->iconButton(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
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
