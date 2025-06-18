<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecapagemResource\Pages;
use App\Filament\Resources\RecapagemResource\RelationManagers;
use App\Models\Recapagem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecapagemResource extends Resource
{
    protected static ?string $model = Recapagem::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Recapagens';

    protected static ?string $pluralLabel = 'Recapagens';

    protected static ?string $label = 'Recapagem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pneu_id')
                    ->label('Pneu')
                    ->relationship('pneu', 'numero_fogo')
                    ->required(),
                Forms\Components\DatePicker::make('data_recapagem')
                    ->required(),
                Forms\Components\Select::make('desenho_pneu_id')
                    ->label('Desenho do Pneu')
                    ->relationship('desenhoPneu', 'modelo')
                    ->required(),
                Forms\Components\Select::make('parceiro_id')
                    ->label('Parceiro')
                    ->relationship('parceiro', 'nome')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_recapagem')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('desenhoPneu.modelo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parceiro.nome')
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
            'index' => Pages\ListRecapagems::route('/'),
            'create' => Pages\CreateRecapagem::route('/create'),
            'edit' => Pages\EditRecapagem::route('/{record}/edit'),
        ];
    }
}
