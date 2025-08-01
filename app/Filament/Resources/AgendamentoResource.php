<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\AgendamentoResource\Pages;
use App\Filament\Resources\AgendamentoResource\RelationManagers;
use App\Models;
use App\Services\Agendamento\AgendamentoService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Models\Agendamento::class;

    protected static ?string $navigationGroup = 'Manutenção';

    protected static ?string $pluralModelLabel = 'Agendamentos';

    protected static ?string $pluralLabel = 'Agendamentos';

    protected static ?string $label = 'Agendamento';

    public static function form(Form $form): Form
    {
        return $form
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 8,
            ])
            ->schema([
                Forms\Components\Fieldset::make('Informações Básicas')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 4,
                        'xl' => 8,
                    ])
                    ->schema([
                        OrdemServicoResource::getVeiculoIdFormField()
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 2,
                                'xl' => 2,
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 1,
                                'lg' => 2,
                                'xl' => 2,
                            ])
                            ->options(StatusOrdemServicoEnum::toSelectArray())
                            ->required()
                            ->default(StatusOrdemServicoEnum::PENDENTE->value)
                            ->selectablePlaceholder(false)
                            ->disableOptionWhen(fn(string $value): bool => in_array($value, [StatusOrdemServicoEnum::VALIDAR->value, StatusOrdemServicoEnum::ADIADO->value])),
                    ]),
                Forms\Components\Fieldset::make('Datas')
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'xl' => 8,
                    ])
                    ->schema([
                        Forms\Components\DatePicker::make('data_agendamento')
                            ->label('Agendado Para')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 1,
                                'xl' => 2,
                            ]),
                        Forms\Components\DatePicker::make('data_limite')
                            ->label('Dt. Limite')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 1,
                                'xl' => 2,
                            ]),
                        Forms\Components\DatePicker::make('data_realizado')
                            ->label('Realizado em')
                            ->maxDate(now()->format('Y-m-d'))
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 1,
                                'xl' => 2,
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Serviço')
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'xl' => 8,
                    ])
                    ->schema([
                        ItemOrdemServicoResource::getServicoIdFormField()
                            ->columnStart(1)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 3,
                                'xl' => 8,
                            ]),
                        ItemOrdemServicoResource::getControlaPosicaoFormField()
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 1,
                                'xl' => 2,
                            ]),
                        ItemOrdemServicoResource::getPosicaoFormField()
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                                'xl' => 2,
                            ]),
                        Forms\Components\Textarea::make('observacao')
                            ->label('Observação')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),
                OrdemServicoResource::getParceiroIdFormField()
                    ->columnSpan(4),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['veiculo', 'ordemServico', 'servico', 'parceiro', 'creator', 'updater']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->width('1%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->label('OS ID')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->placeholder('Sem Vínculo')
                    ->url(fn(Models\Agendamento $record): string => OrdemServicoResource::getUrl('edit', ['record' => $record->ordem_servico_id ?? 0]))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('data_agendamento')
                    ->label('Agendado Para')
                    ->width('1%')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('data_limite')
                    ->label('Dt. Limite')
                    ->width('1%')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('data_realizado')
                    ->label('Finalizado Em')
                    ->width('1%')
                    ->date('d/m/Y')
                    ->placeholder('Não definido')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->label('Fornecedor')
                    ->width('1%')
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Atualizado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('servico_id')
                    ->label('Serviço')
                    ->relationship('servico', 'descricao')
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('parceiro_id')
                    ->label('Fornecedor')
                    ->relationship('parceiro', 'nome')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusOrdemServicoEnum::toSelectArray())
                    ->multiple()
                    ->default([StatusOrdemServicoEnum::PENDENTE->value, StatusOrdemServicoEnum::EXECUCAO->value]),
                Tables\Filters\SelectFilter::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->relationship('ordemServico', 'id')
                    ->searchable()
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('possui_vinculo')
                    ->label('Possui Vinculo c/ OS')
                    ->nullable()
                    ->attribute('ordem_servico_id'),
                Tables\Filters\Filter::make('data_agendamento')
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio')
                            ->label('Dt. Agendada Inicio'),
                        Forms\Components\DatePicker::make('data_fim')
                            ->label('Dt. Agendada Fim'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_agendamento', '>=', $date),
                            )
                            ->when(
                                $data['data_fim'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_agendamento', '<=', $date),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('data_agenda')
                    ->label('Possui Dt. Agendada')
                    ->nullable()
                    ->attribute('data_agendamento')
            ])
            ->groups([
                Tables\Grouping\Group::make('veiculo.placa')
                    ->label('Placa')
                    ->collapsible()
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('veiculo.placa')
            ->defaultSort('data_agendamento', 'asc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['updated_by'] = Auth::user()->id;
                        return $data;
                    }),

            ])
            ->headerActions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->is_admin),
                    Tables\Actions\BulkAction::make('cancelar')
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                           $records->each(function(Models\Agendamento $record) {
                            if ($record->status == StatusOrdemServicoEnum::PENDENTE && $record->ordem_servico_id === null) {
                                (new AgendamentoService($record))->cancelar();
                            }
                        });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
                Tables\Actions\BulkAction::make('gerar-ordem-servico')
                    ->label('Gerar OS')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each(function(Models\Agendamento $record) {
                            if ($record->status == StatusOrdemServicoEnum::PENDENTE && $record->ordem_servico_id === null) {
                                (new AgendamentoService($record))->incluirEmOrdemServico();
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('encerrar')
                    ->label('Encerrar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each(function(Models\Agendamento $record) {
                            if ($record->status == StatusOrdemServicoEnum::EXECUCAO) {
                                (new AgendamentoService($record))->encerrar();
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            // ->checkIfRecordIsSelectableUsing(
            //     fn(Agendamento $record): bool => $record->status == StatusOrdemServicoEnum::PENDENTE && $record->ordem_servico_id === null,
            // )
            ->poll('5s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendamentos::route('/'),
        ];
    }
}
