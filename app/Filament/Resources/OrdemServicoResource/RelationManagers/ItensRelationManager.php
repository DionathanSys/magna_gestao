<?php

namespace App\Filament\Resources\OrdemServicoResource\RelationManagers;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\ItemOrdemServicoResource;
use App\Models\Servico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ItensRelationManager extends RelationManager
{
    protected static string $relationship = 'itens';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ItemOrdemServicoResource::getServicoIdFormField(),
                ItemOrdemServicoResource::getPosicaoFormField()
                    ->required(fn(Forms\Get $get) => Servico::find($get('servico_id'))->controla_posicao ?? false),
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
                    ->label('ID')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('servico.codigo')
                    ->label('Código')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('posicao')
                    ->label('Posição')
                    ->width('1%')
                    ->placeholder('N/A'),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->width('1%')
                    ->options(StatusOrdemServicoEnum::toSelectArray()),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação')
                    ->placeholder('N/A'),
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
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::user()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
