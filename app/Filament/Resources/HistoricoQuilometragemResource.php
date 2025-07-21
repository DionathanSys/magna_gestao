<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricoQuilometragemResource\Pages;
use App\Filament\Resources\HistoricoQuilometragemResource\RelationManagers;
use App\Models\HistoricoQuilometragem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoricoQuilometragemResource extends Resource
{
    protected static ?string $model = HistoricoQuilometragem::class;

    protected static ?string $navigationGroup = 'Veículos';

    protected static ?string $pluralModelLabel = 'Hist. KM';

    protected static ?string $pluralLabel = 'Hist. KM';

    protected static ?string $label = 'Hist. KM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('quilometragem')
                    ->label('Quilometragem')
                    ->required()
                    ->numeric()
                    ->columnSpan(1),
                Forms\Components\DatePicker::make('data_referencia')
                    ->label('Data de Referência')
                    ->required()
                    ->date()
                    ->default(now())
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->columnSpan(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('data_referencia')
                    ->label('Data de Referência')
                    ->sortable()
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('quilometragem')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(null)
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(null),
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
            'index' => Pages\ListHistoricoQuilometragems::route('/'),
            'create' => Pages\CreateHistoricoQuilometragem::route('/create'),
            'edit' => Pages\EditHistoricoQuilometragem::route('/{record}/edit'),
        ];
    }
}
