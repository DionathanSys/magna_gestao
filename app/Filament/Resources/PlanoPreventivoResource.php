<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoPreventivoResource\Pages;
use App\Filament\Resources\PlanoPreventivoResource\RelationManagers;
use App\Models\PlanoPreventivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoPreventivoResource extends Resource
{
    protected static ?string $model = PlanoPreventivo::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Planos Preventivos';

    protected static ?string $pluralLabel = 'Planos Preventivos';

    protected static ?string $label = 'Plano Preventivo';

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
                //
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
            'index' => Pages\ListPlanoPreventivos::route('/'),
            'create' => Pages\CreatePlanoPreventivo::route('/create'),
            'edit' => Pages\EditPlanoPreventivo::route('/{record}/edit'),
        ];
    }
}
