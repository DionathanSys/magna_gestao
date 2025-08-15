<?php

namespace App\Filament\Indicadores\Resources\GestorResource\RelationManagers;

use App\Filament\Indicadores\Resources\IndicadorResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IndicadoresRelationManager extends RelationManager
{
    protected static string $relationship = 'indicadores';

    public function form(Form $form): Form
    {
        return $form

            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descricao')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->width('1%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gestor_id')
                    ->width('1%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->width('1%')
                    ->url(fn($record) => IndicadorResource::getUrl('edit', ['record' => $record->indicador_id]))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('peso')
                    ->label('Peso')
                    ->width('1%')
                    ->numeric(),
                Tables\Columns\TextColumn::make('tipo')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('periodicidade')
                    ->label('Periodicidade'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Indicador')
                    ->icon('heroicon-o-plus'),

                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->modalHeading('Vincular Indicador')
                    ->recordSelect(
                        fn (Forms\Components\Select $select) =>
                            $select
                                ->placeholder('Selecionar Indicador')
                                ->relationship('gestores.indicadores', 'descricao')
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
                ]),
            ])
            ->emptyStateHeading('Sem indicadores vinculados')
            ->emptyStateDescription('Clique no botão acima para adicionar/vincular um indicador.');
    }
}
