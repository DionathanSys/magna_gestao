<?php

namespace App\Filament\Indicadores\Resources\IndicadorResource\RelationManagers;

use App\Filament\Indicadores\Resources\GestorResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GestoresRelationManager extends RelationManager
{
    protected static string $relationship = 'gestores';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nome')
                    ->url(fn($record) => GestorResource::getUrl('edit', ['record' => $record->gestor_id]))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('unidade'),
                Tables\Columns\TextColumn::make('setor'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->iconButton(),
                Tables\Actions\AttachAction::make()
                    ->iconButton()
                    ->preloadRecordSelect()
                    ->modalHeading('Vincular Gestor')
                    ->recordSelect(
                        fn (Forms\Components\Select $select) =>
                            $select
                                ->placeholder('Selecionar Gestor')
                                ->relationship('indicadores.gestores', 'nome')
                                ->preload(),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DetachAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
