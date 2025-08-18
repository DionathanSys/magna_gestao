<?php

namespace App\Filament\Indicadores\Resources\GestorResource\RelationManagers;

use App\Filament\Indicadores\Resources\ResultadoResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResultadosRelationManager extends RelationManager
{
    protected static string $relationship = 'resultados';

    public function form(Form $form): Form
    {
        return ResultadoResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('gestor.nome')
                    ->label('Gestor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.descricao')
                    ->label('Indicador')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pontuacao_obtida')
                    ->label('Pontuação')
                    ->numeric('2', ',' , '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.peso')
                    ->label('Peso Indicador')
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodo')
                    ->label('Período')
                    ->dateTime('F/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Resultado')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['gestor_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
