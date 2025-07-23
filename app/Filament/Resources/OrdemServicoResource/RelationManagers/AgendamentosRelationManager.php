<?php

namespace App\Filament\Resources\OrdemServicoResource\RelationManagers;

use App\Filament\Resources\AgendamentoResource;
use App\Models\Agendamento;
use App\Services\OrdemServico\AgendamentoService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class AgendamentosRelationManager extends RelationManager
{
    protected static string $relationship = 'agendamentosPendentes';

    public function form(Form $form): Form
    {
        return $form
            ->schema(fn(Forms\Form $form) => AgendamentoResource::form($form));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço'),
                Tables\Columns\TextColumn::make('data_agendamento')
                    ->label('Agendado Para')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('data_limite')
                    ->label('Dt. Limite')
                    ->date('d/m/Y')
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('data_finalizado')
                    ->label('Finalizado Em')
                    ->date('d/m/Y')
                    ->placeholder('Não definido')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação')
                    ->placeholder('Não possui'),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->label('Fornecedor')
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Criado Por')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Atualizado Por')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado Em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado Em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    // ->successNotification(null),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    // ->successNotification(null)
                    ->iconButton(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                        // ->successNotification(null),
                    Tables\Actions\BulkAction::make('vincular')
                        // ->successNotification(null)
                        ->label('Incluir na Ordem de Serviço')
                        ->icon('heroicon-o-document-arrow-up')
                        ->action(function (Collection $records) {
                            $records->each(function (Agendamento $agendamento) {
                                (new AgendamentoService)
                                    ->incluirAgendamentosEmOrdemServico(
                                        collect($agendamento));
                            });
                        })
                ]),
            ])
            ->poll('5s');
    }
}
