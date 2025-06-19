<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsertoResource\Pages;
use App\Filament\Resources\ConsertoResource\RelationManagers;
use App\Models\Conserto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsertoResource extends Resource
{
    protected static ?string $model = Conserto::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Consertos';

    protected static ?string $pluralLabel = 'Consertos';

    protected static ?string $label = 'Conserto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pneu_id')
                    ->relationship('pneu', 'numero_fogo')
                    ->required(),
                Forms\Components\DatePicker::make('data_conserto')
                    ->required(),
                Forms\Components\Select::make('tipo_conserto')
                    ->options([
                        'VULGANIZAÇÃO' => 'VULGANIZAÇÃO',
                        'RAC' => 'RAC',
                    ])
                    ->required(),
                Forms\Components\Select::make('parceiro_id')
                    ->label('Parceiro')
                    ->relationship('parceiro', 'nome')
                    ->required(),
                Forms\Components\TextInput::make('valor_conserto')
                    ->label('Valor do Conserto')
                    ->prefix('R$')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Toggle::make('garantia')
                    ->label('Com Garantia')
                    ->default(true)
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
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_conserto')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_conserto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_conserto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('garantia')
                    ->boolean(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->numeric()
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
            'index' => Pages\ListConsertos::route('/'),
            // 'create' => Pages\CreateConserto::route('/create'),
            // 'edit' => Pages\EditConserto::route('/{record}/edit'),
        ];
    }
}
