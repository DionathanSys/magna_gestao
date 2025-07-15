<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\AgendamentoResource\Pages;
use App\Filament\Resources\AgendamentoResource\RelationManagers;
use App\Models\Agendamento;
use App\Models\OrdemServico;
use App\Services\OrdemServico\AgendamentoService;
use App\Services\OrdemServico\OrdemServicoService;
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
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Agendamentos';

    protected static ?string $pluralLabel = 'Agendamentos';

    protected static ?string $label = 'Agendamento';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                Forms\Components\Fieldset::make('Informações Básicas')
                    ->columns(8)
                    ->schema([
                        Forms\Components\TextInput::make('ordem_servico_id')
                            ->label('Ordem de Serviço')
                            ->columnSpan(2)
                            ->visible(fn() => Auth::user()->is_admin)
                            ->readOnly(fn() => ! Auth::user()->is_admin)
                            ->numeric(),
                        OrdemServicoResource::getVeiculoIdFormField()
                            ->columnSpan(2)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->columnSpan(2)
                            ->options(StatusOrdemServicoEnum::toSelectArray())
                            ->required()
                            ->default(StatusOrdemServicoEnum::PENDENTE->value)
                            ->selectablePlaceholder(false)
                            ->disableOptionWhen(fn(string $value): bool => in_array($value, [StatusOrdemServicoEnum::VALIDAR->value, StatusOrdemServicoEnum::ADIADO->value])),
                    ]),
                Forms\Components\Fieldset::make('Datas')
                    ->columns(8)
                    ->schema([
                        Forms\Components\DatePicker::make('data_agendamento')
                            ->label('Agendado Para')
                            ->minDate(now())
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('data_limite')
                            ->label('Dt. Limite')
                            ->minDate(now())
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('data_realizado')
                            ->label('Realizado em')
                            ->minDate(now())
                            ->columnSpan(2),
                    ]),
                Forms\Components\Fieldset::make('Datas')
                    ->columns(8)
                    ->schema([
                        ItemOrdemServicoResource::getServicoIdFormField()
                            ->columnStart(1)
                            ->columnSpan(4),
                        ItemOrdemServicoResource::getControlaPosicaoFormField()
                            ->columnSpan(2),
                        ItemOrdemServicoResource::getPosicaoFormField()
                            ->columnSpan(1),
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
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->label('OS ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_agendamento')
                    ->label('Agendado Para')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_limite')
                    ->label('Dt. Limite')
                    ->date('d/m/Y')
                    ->placeholder('Não definido'),
                Tables\Columns\TextColumn::make('data_finalizado')
                    ->label('Finalizado Em')
                    ->date('d/m/Y')
                    ->placeholder('Não definido')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação'),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->label('Fornecedor')
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
                Tables\Filters\SelectFilter::make('parceiro_id')
                    ->label('Fornecedor')
                    ->relationship('parceiro', 'nome')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusOrdemServicoEnum::toSelectArray())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->relationship('ordemServico', 'id')
                    ->searchable()
                    ->multiple(),
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
            ])
            ->groups([
                Tables\Grouping\Group::make('veiculo.placa')
                    ->label('Placa')
                    ->collapsible()
            ])
            ->defaultGroup('veiculo.placa')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['updated_by'] = Auth::user()->id;
                        return $data;
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('gerar-ordem-servico')
                        ->label('Gerar OS')
                        ->icon('heroicon-o-forward')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            (new AgendamentoService())
                                ->incluirAgendamentosEmOrdemServico($records);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])->checkIfRecordIsSelectableUsing(
            fn (Agendamento $record): bool => $record->status == StatusOrdemServicoEnum::PENDENTE,
        );
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
