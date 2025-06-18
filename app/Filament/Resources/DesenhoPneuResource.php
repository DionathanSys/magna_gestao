<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesenhoPneuResource\Pages;
use App\Filament\Resources\DesenhoPneuResource\RelationManagers;
use App\Models\DesenhoPneu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesenhoPneuResource extends Resource
{
    protected static ?string $model = DesenhoPneu::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Desenhos Pneu';

    protected static ?string $pluralLabel = 'Desenhos Pneu';

    protected static ?string $label = 'Desenho Pneus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('medida')
                    ->label('Medida Borracha')
                    ->nullable()
                    ->maxLength(50),
                Forms\Components\TextInput::make('modelo')
                    ->label('Modelo')
                    ->nullable()
                    ->maxLength(100),
                Forms\Components\TextInput::make('estado_pneu')
                    ->label('Estado do Pneu')
                    ->nullable()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modelo')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('medida')
                    ->label('Medida Borracha')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_pneu')
                    ->label('Estado pneu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
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
            'index' => Pages\ListDesenhoPneus::route('/'),
            // 'create' => Pages\CreateDesenhoPneu::route('/create'),
            // 'edit' => Pages\EditDesenhoPneu::route('/{record}/edit'),
        ];
    }
}
