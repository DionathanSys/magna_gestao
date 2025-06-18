<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PneuResource\Pages;
use App\Filament\Resources\PneuResource\RelationManagers;
use App\Models\Pneu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PneuResource extends Resource
{
    protected static ?string $model = Pneu::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Pneus';

    protected static ?string $pluralLabel = 'Pneus';

    protected static ?string $label = 'Pneu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_fogo')
                    ->label('Nº de Fogo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('marca')
                    ->maxLength(255),
                Forms\Components\TextInput::make('modelo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('medida')
                    ->maxLength(255),
                Forms\Components\Select::make('desenho_pneu_id')
                    ->relationship('desenhoPneu', 'modelo'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('local')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('data_aquisicao'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_fogo')
                    ->label('Nº de Fogo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('modelo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('medida')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desenhoPneu.medida')
                    ->label('Medida Desenho Pneu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('local')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_aquisicao')
                    ->label('Dt. Aquisição')
                    ->date()
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
            'index' => Pages\ListPneus::route('/'),
            // 'create' => Pages\CreatePneu::route('/create'),
            // 'edit' => Pages\EditPneu::route('/{record}/edit'),
        ];
    }
}
