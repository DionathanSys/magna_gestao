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

class AgendamentosRelationManager extends RelationManager
{
    protected static string $relationship = 'agendamentos';

    protected AgendamentoService $agendamentoService;

    public function __construct()
    {
        $this->agendamentoService = new AgendamentoService();
    }

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
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Criado Por'),
                Tables\Columns\TextColumn::make('updator.name')
                    ->label('Atualizado Por'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado Em')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado Em')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('vincular')
                    ->label('Incluir na Ordem de Serviço')
                    ->action(function (Agendamento $record) {
                        $this->agendamentoService->vincularServico($record, $this->ownerRecord);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
