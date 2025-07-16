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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_referencia')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('quilometragem')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListHistoricoQuilometragems::route('/'),
            'create' => Pages\CreateHistoricoQuilometragem::route('/create'),
            'edit' => Pages\EditHistoricoQuilometragem::route('/{record}/edit'),
        ];
    }
}
