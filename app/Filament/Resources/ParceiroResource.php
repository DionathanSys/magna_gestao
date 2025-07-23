<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParceiroResource\Pages;
use App\Filament\Resources\ParceiroResource\RelationManagers;
use App\Models\Parceiro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParceiroResource extends Resource
{
    protected static ?string $model = Parceiro::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                static::getNomeParceiroFormField()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    // ->successNotification(null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        // ->successNotification(null),
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
            'index' => Pages\ListParceiros::route('/'),
            'create' => Pages\CreateParceiro::route('/create'),
            'edit' => Pages\EditParceiro::route('/{record}/edit'),
        ];
    }

    public static function getNomeParceiroFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('nome')
            ->label('Nome do Parceiro')
            ->required()
            ->autocomplete(false)
            ->maxLength(255);
    }

}
