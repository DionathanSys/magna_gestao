<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Enum\OrdemServico\TipoManutencaoEnum;
use App\Filament\Resources\OrdemServicoResource\Pages;
use App\Filament\Resources\OrdemServicoResource\RelationManagers;
use App\Filament\Resources\OrdemServicoResource\RelationManagers\AgendamentosRelationManager;
use App\Filament\Resources\OrdemServicoResource\RelationManagers\ItensRelationManager;
use App\Filament\Resources\OrdemServicoResource\RelationManagers\PlanoPreventivoRelationManager;
use App\Models\Agendamento;
use App\Models\ItemOrdemServico;
use App\Models\OrdemSankhya;
use App\Models\OrdemServico;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\NotificacaoService as notify;
use App\Services\OrdemServico\ItemOrdemServicoService;
use App\Services\OrdemServico\OrdemServicoService;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\ActionsPosition;
use Fauzie811\FilamentListEntry;
use Fauzie811\FilamentListEntry\FilamentListEntryPlugin;
use Fauzie811\FilamentListEntry\Infolists\Components\ListEntry;
use Illuminate\Support\Facades\Auth;

class OrdemServicoResource extends Resource
{
    protected static ?string $model = OrdemServico::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Ordens de Serviço';

    protected static ?string $pluralLabel = 'Ordens de Serviço';

    protected static ?string $label = 'Ordem de Serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->columns([
                'sm' => 1,
                'md' => 4,
                'lg' => 10,
            ])
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informações')
                            ->columns([
                                'sm' => 1,
                                'md' => 4,
                                'lg' => 10,
                            ])
                            ->schema([
                                static::getVeiculoIdFormField()
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),
                                static::getQuilometragemFormField()
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),
                                static::getTipoManutencaoFormField()
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),
                                static::getDataInicioFormField()
                                    ->columnStart(1)
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),
                                static::getDataFimFormField()
                                    ->visibleOn('edit')
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),
                                static::getStatusFormField()
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),
                                static::getStatusSankhyaFormField()
                                    ->columnSpan([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ]),

                                Forms\Components\Section::make('Manutenção Externa')
                                    ->columnSpanFull()
                                    ->columns(8)
                                    ->schema([
                                        static::getParceiroIdFormField()
                                            ->columnSpan(4),
                                    ])
                                    ->collapsed()
                                    ->collapsible(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Ordens Sankhya')
                            ->columns(8)
                            ->visibleOn('edit')
                            ->schema([
                                Forms\Components\Repeater::make('sankhyaId')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('ordem_sankhya_id')
                                            ->label('ID Sankhya')
                                            ->readOnly()
                                            ->numeric()
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(2)
                                    ->defaultItems(0)
                                    ->addable(false),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('sankhyaId.ordem_sankhya_id')
                    ->label('OS Sankhya'),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Veículo'),
                Tables\Columns\TextColumn::make('quilometragem')
                    ->label('Quilometragem')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipo_manutencao')
                    ->label('Tipo Manutenção'),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->label('Dt. Inicio')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('data_fim')
                    ->label('Dt. Fim')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('itens_count')->counts('itens')
                    ->label('Qtd. Serviços'),
                Tables\Columns\TextColumn::make('pendentes_count')->counts('pendentes')
                    ->label('Pendencias')
                    ->color(fn($state): string => $state == 0 ? 'gray' : 'info')
                    ->badge(fn($state): bool => $state > 0),
                Tables\Columns\TextColumn::make('status')
                    ->badge('success'),
                Tables\Columns\SelectColumn::make('status_sankhya')
                    ->label('Sankhya')
                    ->options(StatusOrdemServicoEnum::toSelectArray()),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->label('Fornecedor')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Criado Em')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Editado Em')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->persistFiltersInSession()
            ->defaultSort('veiculo_id')
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
                Tables\Filters\SelectFilter::make('tipo_manutencao')
                    ->label('Tipo Manutenção')
                    ->options(TipoManutencaoEnum::toSelectArray())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusOrdemServicoEnum::toSelectArray())
                    ->multiple(),
                Tables\Filters\Filter::make('data_inicio')
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio')
                            ->label('Dt. Abertura de'),
                        Forms\Components\DatePicker::make('data_fim')
                            ->label('Dt. Abertura até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_inicio', '>=', $date),
                            )
                            ->when(
                                $data['data_fim'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_inicio', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('encerrar')
                        ->successNotification(null)
                        ->label('Encerrar OS')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn(OrdemServico $record) => (new OrdemServicoService)->encerrarOrdemServico($record)),
                    Tables\Actions\EditAction::make()
                        ->successNotification(null),
                    Tables\Actions\Action::make('ordem_sankhya')
                        ->successNotification(null)
                        ->label('Add Ordem Sankhya')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->modal()
                        ->modalHeading('Vincular OS Sankhya')
                        ->modalDescription('Preencha o ID da Ordem de Serviço no Sankhya.')
                        ->modalIcon('heroicon-o-document-plus')
                        ->modalWidth(MaxWidth::Large)
                        ->modalAlignment(Alignment::Center)
                        ->extraModalFooterActions(fn(\Filament\Tables\Actions\Action $action): array => [
                            $action->makeModalSubmitAction('vincularOutro', arguments: ['another' => true]),
                        ])
                        ->modalSubmitActionLabel('Vincular')
                        ->form(fn(Forms\Form $form) => $form
                            ->columns(8)
                            ->schema([
                                Forms\Components\TextInput::make('ordem_sankhya_id')
                                    ->label('ID Sankhya')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        $exists = OrdemSankhya::where('ordem_sankhya_id', $state)->exists();
                                        $set('existe', $exists ? 'Sim' : 'Não');
                                    }),
                                Forms\Components\TextInput::make('existe')
                                    ->label('Já existe?')
                                    ->readOnly()
                                    ->live()
                                    ->columnSpan(2),
                            ]))
                        ->action(function (Tables\Actions\Action $action, Form $form, OrdemServico $record, array $data, array $arguments) {
                            if ($data['existe'] == 'Sim') {
                                notify::error('Ordem de Serviço Sankhya já vinculada!');
                                $action->halt();
                            }

                            OrdemSankhya::create([
                                'ordem_servico_id' => $record->id,
                                'ordem_sankhya_id' => $data['ordem_sankhya_id'],
                            ]);

                            if ($arguments['another'] ?? false) {
                                $form->fill();
                                $action->halt();
                            }

                            return;
                        })
                ])
                    ->icon('heroicon-o-bars-3-center-left'),
                Tables\Actions\ViewAction::make()
                    ->successNotification(null)
                    ->label('Visualizar')
                    ->color('primary')
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->iconButton(),
                Tables\Actions\Action::make('add-item')
                    ->successNotification(null)
                    ->label('Adicionar Item')
                    ->icon('heroicon-o-plus')
                    ->iconButton()
                    ->tooltip('Adicionar Item')
                    ->extraModalFooterActions(fn(\Filament\Tables\Actions\Action $action): array => [
                            $action->makeModalSubmitAction('adicionarOutro', arguments: ['another' => true]),
                        ])
                    ->form(fn(Forms\Form $form) => $form
                        ->columns([
                            'sm' => 1,
                            'md' => 4,
                            'lg' => 8,
                        ])
                        ->schema([
                            ItemOrdemServicoResource::getServicoIdFormField()
                                ->columnStart(1)
                                ->columnSpan([
                                    'sm' => 1,
                                    'md' => 2,
                                    'lg' => 3
                                ]),
                            ItemOrdemServicoResource::getControlaPosicaoFormField()
                                ->columnSpan([
                                    'sm' => 1,
                                    'md' => 1,
                                    'lg' => 2
                                ]),
                            ItemOrdemServicoResource::getPosicaoFormField()
                                ->columnSpan([
                                    'sm' => 1,
                                    'md' => 1,
                                    'lg' => 2
                                ]),
                            ItemOrdemServicoResource::getStatusFormField()
                                ->columnSpan([
                                    'sm' => 1,
                                    'md' => 2,
                                    'lg' => 3
                                ]),
                            ItemOrdemServicoResource::getObersavacaoFormField()
                                ->columnSpanFull(),
                        ]))
                    ->mutateFormDataUsing(function (OrdemServico $record, array $data): array {
                        $data['ordem_servico_id'] = $record->id;
                        $data['created_by'] = Auth::user()->id;
                        return $data;
                    })
                    ->action(function(Tables\Actions\Action $action, Form $form, array $data) {

                        ItemOrdemServicoService::create($data);

                        if ($arguments['another'] ?? false) {
                                $form->fill();
                                $action->halt();
                            }

                        return;
                    }),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(null)
                        ->visible(fn() => Auth::user()->is_admin),
                ]),
            ])
            ->poll('5s')
            ->emptyStateDescription('');
    }

    public static function infoList(Infolist $infoList): InfoList
    {
        return $infoList
            ->columns(8)
            ->schema([
                Infolists\Components\TextEntry::make('veiculo.placa')
                    ->label('Veículo')
                    ->badge()
                    ->color('primary')
                    ->columnSpan(2),
                Infolists\Components\TextEntry::make('quilometragem')
                    ->label('Quilometragem')
                    ->columnSpan(2)
                    ->placeholder('Não informado'),
                Infolists\Components\TextEntry::make('tipo_manutencao')
                    ->label('Tipo Manutenção')
                    ->columnSpan(2),
                Infolists\Components\TextEntry::make('data_inicio')
                    ->label('Data Início')
                    ->columnSpan(2)
                    ->dateTime('d/m/Y H:i'),
                Infolists\Components\RepeatableEntry::make('itens')
                    ->label('Serviços')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        Infolists\Components\TextEntry::make('servico.codigo')
                            ->label('Código')
                            ->columnSpan(1),
                        Infolists\Components\TextEntry::make('servico.descricao')
                            ->label('Serviço')
                            ->columnSpan(4)
                            ->formatStateUsing(fn(ItemOrdemServico $item) => $item->servico->descricao),
                        Infolists\Components\TextEntry::make('posicao')
                            ->label('Posição')
                            ->columnSpan(1)
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->columnSpan(2)
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('observacao')
                            ->label('Observação')
                            ->columnSpan(4)
                            ->placeholder('Sem observações'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItensRelationManager::class,
            AgendamentosRelationManager::class,
            PlanoPreventivoRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdemServicos::route('/'),
            'edit' => Pages\EditOrdemServico::route('/{record}/edit'),
        ];
    }

    public static function getVeiculoIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('veiculo_id')
            ->label('Veículo')
            ->searchPrompt('Buscar Placa')
            ->placeholder('Buscar ...')
            ->columnSpan(2)
            ->required()
            ->relationship('veiculo', 'placa')
            ->searchable()
            ->preload()
            ->live(onBlur: true)
            ->afterStateUpdated(function (Forms\Set $set, $state) {
                if ($state) {
                    $veiculo = \App\Models\Veiculo::with('kmAtual')->find($state);
                    if ($veiculo) {
                        $set('quilometragem', $veiculo->kmAtual?->quilometragem ?? 0);
                    }
                } else {
                    $set('quilometragem', null);
                }
            });
    }

    public static function getQuilometragemFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('quilometragem')
            ->label('Quilometragem')
            ->columnSpan(2)
            ->numeric()
            ->minValue(0)
            ->maxValue(999999)
            ->required();
    }

    public static function getTipoManutencaoFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('tipo_manutencao')
            ->label('Tipo de Manutenção')
            ->columnSpan(2)
            ->options(TipoManutencaoEnum::toSelectArray())
            ->required()
            ->default(TipoManutencaoEnum::CORRETIVA->value);
    }

    public static function getDataInicioFormField(): Forms\Components\DateTimePicker
    {
        return Forms\Components\DateTimePicker::make('data_inicio')
            ->label('Dt. Inicio')
            ->columnSpan(2)
            ->seconds(false)
            ->required()
            ->maxDate(now())
            ->default(now());
    }

    public static function getDataFimFormField(): Forms\Components\DateTimePicker
    {
        return Forms\Components\DateTimePicker::make('data_fim')
            ->label('Dt. Fim')
            ->columnSpan(2)
            ->seconds(false)
            ->maxDate(now());
    }

    public static function getStatusFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status')
            ->label('Status')
            ->columnSpan(2)
            ->options(StatusOrdemServicoEnum::toSelectArray())
            ->default(StatusOrdemServicoEnum::PENDENTE->value)
            ->required();
    }

    public static function getStatusSankhyaFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status_sankhya')
            ->label('Sankhya')
            ->columnSpan(2)
            ->options(StatusOrdemServicoEnum::toSelectArray())
            ->default(StatusOrdemServicoEnum::PENDENTE->value)
            ->required();
    }

    public static function getParceiroIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('parceiro_id')
            ->label('Parceiro')
            ->columnSpan(2)
            ->relationship('parceiro', 'nome')
            ->searchable()
            ->preload()
            ->searchPrompt('Buscar Parceiro')
            ->placeholder('Buscar ...')
            ->createOptionForm(fn(Forms\Form $form) => ParceiroResource::form($form))
            ->editOptionForm(fn(Forms\Form $form) => ParceiroResource::form($form));
    }
}
