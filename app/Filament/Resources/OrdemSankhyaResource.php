<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdemSankhyaResource\Pages;
use App\Filament\Resources\OrdemSankhyaResource\RelationManagers;
use App\Models\OrdemSankhya;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdemSankhyaResource extends Resource
{
    protected static ?string $model = OrdemSankhya::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Ordens Sankhya';

    protected static ?string $pluralLabel = 'Ordens Sankhya';

    protected static ?string $label = 'Ordem Sankhya';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ordem_servico_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ordem_sankhya_id')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordem_sankhya_id')
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
                Tables\Actions\EditAction::make()
                    ->successNotification(null),
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
            'index' => Pages\ListOrdemSankhyas::route('/'),
            'create' => Pages\CreateOrdemSankhya::route('/create'),
            'edit' => Pages\EditOrdemSankhya::route('/{record}/edit'),
        ];
    }
}
