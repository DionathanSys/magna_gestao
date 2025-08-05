<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntegradoResource\Pages;
use App\Filament\Resources\IntegradoResource\RelationManagers;
use App\Models\Integrado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IntegradoResource extends Resource
{
    protected static ?string $model = Integrado::class;

    protected static ?string $navigationGroup = 'Parceiros';

    protected static ?string $pluralModelLabel = 'Integrados';

    protected static ?string $pluralLabel = 'Integrados';

    protected static ?string $label = 'Integrado';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->autocomplete(false)
                    ->required(),
                Forms\Components\TextInput::make('nome')
                    ->autocomplete(false)
                    ->required(),
                Forms\Components\TextInput::make('km_rota')
                    ->autocomplete(false)
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('municipio'),
                Forms\Components\TextInput::make('estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('km_rota')
                    ->numeric(2, ',', '.'),
                Tables\Columns\TextColumn::make('municipio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
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
            ->deferFilters()
            ->searchOnBlur()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
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
            'index' => Pages\ListIntegrados::route('/'),
            'create' => Pages\CreateIntegrado::route('/create'),
            'edit' => Pages\EditIntegrado::route('/{record}/edit'),
        ];
    }
}
