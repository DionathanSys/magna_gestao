<?php

namespace App\Filament\Resources\OrdemServicoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PlanoPreventivoRelationManager extends RelationManager
{
    protected static string $relationship = 'planoPreventivoVinculado';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ordem_servico_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Plano')
                    ->sortable(),
                Tables\Columns\TextColumn::make('plano_preventivo_id')
                    ->label('ID Plano')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planoPreventivo.descricao')
                    ->label('Descrição')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planoPreventivo.intervalo')
                    ->label('Intervalo (km)')
                    ->numeric(0, ',', '.')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->is_admin)
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->visible(fn() => Auth::user()->is_admin)
                ]),
            ]);
    }
}
