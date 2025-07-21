<?php

namespace App\Filament\Resources\PlanoPreventivoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class OrdensServicoRelationManager extends RelationManager
{
    protected static string $relationship = 'ordensServico';

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
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo_id')
                    ->label('Veículo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_execucao')
                    ->label('KM Execução')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_execucao')
                    ->label('Data Execução')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->relationship('ordemServico', 'descricao')
                    ->searchable(),
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(null)
                    ->visible(fn() => Auth::user()->is_admin),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(null)
                    ->visible(fn() => Auth::user()->is_admin),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(null)
                        ->visible(fn() => Auth::user()->is_admin),
                ]),
            ]);
    }
}
