<?php

namespace App\Filament\Indicadores\Resources\GestorResource\RelationManagers;

use App\Filament\Indicadores\Resources\ResultadoResource;
use App\Services\Indicador\IndicadorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\NotificacaoService as notify;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Log;

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
                Tables\Columns\TextColumn::make('indicador.descricao')
                    ->label('Indicador')
                    ->icon(fn($record) => match ($record->indicador->tipo_avaliacao) {
                        'maior_melhor' => 'heroicon-o-arrow-trending-up',
                        'menor_melhor' => 'heroicon-m-arrow-trending-down',
                    })
                    ->iconPosition(IconPosition::After)
                    ->iconColor(fn($record) => match ($record->indicador->tipo_avaliacao) {
                        'maior_melhor' => 'info',
                        'menor_melhor' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('objetivo')
                    ->label('Objetivo')
                    ->formatStateUsing(function($record, $state) {
                        return match($record->indicador->tipo_meta) {
                            '%' => number_format($state, 2, ',', '.') . '%',
                            'R$' => 'R$ ' . number_format($state, 2, ',', '.'),
                            default => $state,
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('resultado')
                    ->label('Resultado')
                    ->formatStateUsing(function($record, $state) {
                        return match($record->indicador->tipo_meta) {
                            '%' => number_format($state, 2, ',', '.') . '%',
                            'R$' => 'R$ ' . number_format($state, 2, ',', '.'),
                            default => $state,
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('pontuacao_obtida')
                    ->label('Pontuação')
                    ->numeric('2', ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.peso_por_periodo')
                    ->label('% Obtido')
                    ->formatStateUsing(fn($record, $state) => number_format(($record->pontuacao_obtida / $state) * 100, 2, ',', '.'))
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(fn($record) => match ($record->status) {
                        'n_atingido' => 'Não Atingido',
                        'parcialmente_atingido' => 'Parcialmente Atingido',
                        'atingido' => 'Atingido',
                    })
                    ->color(fn($record) => match ($record->status) {
                        'n_atingido' => 'danger',
                        'parcialmente_atingido' => 'warning',
                        'atingido' => 'info',
                    })
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodo')
                    ->label('Período')
                    ->dateTime('F/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('tipo_avaliacao')
                    ->label('Tipo de Avaliação')
                    ->state(fn($state) => match ($state) {
                        'maior_melhor' => 'Maior',
                        'menor_melhor' => 'Menor',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    })
                    ->action(function (array $data) {
                        $service = new IndicadorService();
                        $service->createResultado($data);

                        if ($service->hasError()) {
                            notify::error();
                            return;
                        }

                        notify::success();
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
