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
                Tables\Columns\TextColumn::make('pneu_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_movimento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_inicial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('km_final')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eixo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('posicao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sulco_movimento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_movimento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('motivo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observacao')
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
            'index' => Pages\ListHistoricoMovimentoPneus::route('/'),
            'create' => Pages\CreateHistoricoMovimentoPneu::route('/create'),
            'edit' => Pages\EditHistoricoMovimentoPneu::route('/{record}/edit'),
        ];
    }
}
