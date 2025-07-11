<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Enum\OrdemServico\TipoManutencaoEnum;
use App\Filament\Resources\OrdemServicoResource\Pages;
use App\Filament\Resources\OrdemServicoResource\RelationManagers;
use App\Filament\Resources\OrdemServicoResource\RelationManagers\ItensRelationManager;
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
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\ActionsPosition;
use Fauzie811\FilamentListEntry;
use Fauzie811\FilamentListEntry\FilamentListEntryPlugin;
use Fauzie811\FilamentListEntry\Infolists\Components\ListEntry;

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
            ->columns(8)
            ->schema([
                Forms\Components\Section::make('Informações Gerais')
                    ->columnSpanFull()
                    ->columns(8)
                    ->schema([
                        static::getVeiculoIdFormField(),
                        static::getQuilometragemFormField(),
                        static::getTipoManutencaoFormField(),
                        static::getDataInicioFormField()
                            ->columnStart(1)
                            ->columnSpan(2),
                        static::getDataFimFormField()
                            ->visibleOn('edit')
                            ->columnSpan(2),
                        static::getStatusFormField()
                            ->columnSpan(2),
                        static::getStatusSankhyaFormField()
                            ->columnSpan(2),
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
                Tables\Columns\TextColumn::make('status')
                    ->badge('succecs'),
                Tables\Columns\TextColumn::make('status_sankhya')
                    ->label('Sankhya')
                    ->badge('warning'),
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
            ])
            ->persistFiltersInSession()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Visualizar')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('ordem_sankhya')
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
                                notify::success('Ordem Sankhya vinculada!');
                                $action->halt();
                            }

                            return;
                        })
                ])
                    ->icon('heroicon-o-bars-3-center-left')
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll(null)
            ->emptyStateDescription('');
    }

    public static function infoList(Infolist $infoList): InfoList
    {
        return $infoList
            ->columns(8)
            ->schema([
                Infolists\Components\TextEntry::make('veiculo.placa')
                    ->label('Veículo')
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
                    ->columns(8)
                    ->schema([
                        Infolists\Components\TextEntry::make('servico.codigo')
                            ->label('Código')
                            ->columnSpan(1),
                        Infolists\Components\TextEntry::make('servico.descricao')
                            ->label('Serviço')
                            ->columnSpan(4),
                        Infolists\Components\TextEntry::make('posicao')
                            ->label('Posição')
                            ->columnSpan(1)
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->columnSpan(2)
                            ->suffixAction(
                                Infolists\Components\Actions\Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-check')
                                    ->action(function (ItemOrdemServico $record) {
                                        dd($record);
                                    })),
                        Infolists\Components\TextEntry::make('observacao')
                            ->label('Observação')
                            ->columnSpanFull()
                            ->placeholder('Sem observações'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItensRelationManager::class,
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
            ->preload();
    }

    public static function getQuilometragemFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('quilometragem')
            ->label('Quilometragem')
            ->columnSpan(2)
            ->numeric()
            ->minValue(0)
            ->maxValue(999999);
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
            ->preload();
    }
}
