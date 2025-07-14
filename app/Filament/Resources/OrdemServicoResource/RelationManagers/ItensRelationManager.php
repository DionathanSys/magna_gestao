<?php

namespace App\Filament\Resources\OrdemServicoResource\RelationManagers;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\ItemOrdemServicoResource;
use App\Models\ItemOrdemServico;
use App\Models\Servico;
use App\Services\OrdemServico\OrdemServicoService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificacaoService as notify;

class ItensRelationManager extends RelationManager
{
    protected static string $relationship = 'itens';

    protected OrdemServicoService $ordemServicoService;

    public function __construct()
    {
        $this->ordemServicoService = new OrdemServicoService();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                ItemOrdemServicoResource::getServicoIdFormField()
                    ->columnStart(1)
                    ->columnSpan(4),
                ItemOrdemServicoResource::getControlaPosicaoFormField()
                    ->columnSpan(2),
                ItemOrdemServicoResource::getPosicaoFormField()
                    ->columnSpan(1)
                    ->required(fn(ItemOrdemServico $record): bool => $record->servico->controla_posicao ?? false),
                ItemOrdemServicoResource::getStatusFormField()
                    ->columnSpan(2),
                ItemOrdemServicoResource::getObersavacaoFormField()
                    ->columnSpanFull(),

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
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('visualizar-comentarios')
                        ->modalHeading('Comentários')
                        ->slideOver()
                        ->modalSubmitAction(false)
                        ->infolist([
                            \Filament\Infolists\Components\RepeatableEntry::make('comentarios')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('conteudo')
                                        ->label('Comentário'),
                                    \Filament\Infolists\Components\TextEntry::make('created_at')
                                        ->label('Criado em')
                                        ->dateTime('d/m/Y H:i'),
                                ])
                        ])->icon('heroicon-o-chat-bubble-left-ellipsis'),
                    Tables\Actions\Action::make('comentarios')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->form([
                            Forms\Components\Textarea::make('conteudo')
                                ->label('Comentário')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->action(function (array $data, ItemOrdemServico $item) {
                            $item->comentarios()->create([
                                'veiculo_id'    => $item->ordemServico->veiculo_id,
                                'conteudo'      => $data['conteudo'],
                            ]);
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-o-bars-3-center-left')
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('reagendar')
                        ->label('Reagendar')
                        ->icon('heroicon-o-calendar')
                        ->form([
                            Forms\Components\DateTimePicker::make('data_agendamento')
                                ->label('Agendar Para')
                                ->minDate(now()),
                        ])
                        ->action(function (array $data, Collection $records) {
                            $records->each(function (ItemOrdemServico $item) use ($data) {
                                $this->ordemServicoService->reagendarServico($item, $data['data_agendamento'] ?? null);
                            });
                        }),
                ]),
            ]);
    }
}
