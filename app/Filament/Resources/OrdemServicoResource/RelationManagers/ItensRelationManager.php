<?php

namespace App\Filament\Resources\OrdemServicoResource\RelationManagers;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\ItemOrdemServicoResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItensRelationManager extends RelationManager
{
    protected static string $relationship = 'itens';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ItemOrdemServicoResource::getServicoIdFormField(),
                ItemOrdemServicoResource::getPosicaoFormField(),
                ItemOrdemServicoResource::getObersavacaoFormField(),
                ItemOrdemServicoResource::getStatusFormField(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço'),
                Tables\Columns\TextColumn::make('posicao')
                    ->label('Posição'),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação'),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options(StatusOrdemServicoEnum::toSelectArray()),
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
                Tables\Columns\TextColumn::make('created_by.name')
                    ->label('Criado por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Serviço')
                    ->icon('heroicon-o-plus'),
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
