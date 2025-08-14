<?php

namespace App\Filament\Resources;

use Closure;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ViagemResource\Pages;
use App\Filament\Resources\ViagemResource\RelationManagers;
use App\Models\CargaViagem;
use App\Models\Integrado;
use App\Models\Viagem;
use App\Enum\MotivoDivergenciaViagem;
use App\Filament\Resources\ViagemResource\Widgets\AdvancedStatsOverviewWidget;
use App\Infolists\Components\InfoViagem;
use App\Services\CargaService;
use App\Services\IntegradoService;
use App\Services\ViagemService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\NotificacaoService as notify;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportEvents\HandlesEvents;
use App\Jobs\RegistrarViagemComplementoJob;
use App\Services\Viagem\ViagemComplementoService;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\Summarizers\Range;
use Illuminate\Support\Facades\Cache;

class ViagemResource extends Resource
{

    use HandlesEvents;

    protected static ?string $model = Viagem::class;

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Viagens';

    protected static ?string $pluralLabel = 'Viagens';

    protected static ?string $label = 'Viagem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->required()
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->visibleOn('create'),
                Forms\Components\Section::make('Viagem')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('numero_viagem')
                            ->required(),
                        Forms\Components\TextInput::make('numero_custo_frete')
                            ->label('Nº Custo Frete')
                            ->numeric(),
                        Forms\Components\TextInput::make('documento_transporte')
                            ->label('Doc. Transp.')
                            ->numeric(),
                        Forms\Components\Select::make('tipo_viagem')
                            ->label('Tipo Viagem')
                            ->options([
                                'SIMPLES' => 'Simples',
                                'COMPOSTA' => 'Composta',
                            ])
                            ->required(),
                    ]),
                Forms\Components\Section::make('Quilometragens')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('km_rodado')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_pago')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_cadastro')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_pago_excedente')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_rodado_excedente')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_cobrar')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_rota_corrigido')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('motivo_divergencia')
                            ->label('Motivo Divergência')
                            ->options(MotivoDivergenciaViagem::toSelectArray())
                            ->default(MotivoDivergenciaViagem::DESLOCAMENTO_OUTROS->value),
                    ]),
                Forms\Components\Section::make('Datas')
                    ->columns(4)
                    ->schema([
                        Forms\Components\DatePicker::make('data_competencia')
                            ->required(),
                        Forms\Components\DateTimePicker::make('data_inicio')
                            ->required(),
                        Forms\Components\DateTimePicker::make('data_fim')
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('peso')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('entregas')
                    ->required()
                    ->numeric()
                    ->default(1),

                Forms\Components\Toggle::make('conferido')
                    ->required(),
                Forms\Components\KeyValue::make('divergencias')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('carga.integrado', 'veiculo');
            })
            ->poll(null)
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('numero_viagem')
                    ->label('Nº Viagem')
                    ->width('1%')
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('cargas.integrado.codigo')
                    ->label('Cód. Integrado')
                    ->width('1%')
                    ->listWithLineBreaks()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cargas.integrado.nome')
                    ->label('Integrado')
                    ->width('1%')
                    ->tooltip(fn(Viagem $record) => $record->carga->integrado?->codigo ?? 'N/A')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->label('Doc. Transp.')
                    ->width('1%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColumnGroup::make('KM', [
                    Tables\Columns\TextColumn::make('km_rodado')
                        ->width('1%')
                        ->wrapHeader()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                    Tables\Columns\TextColumn::make('km_pago')
                        ->width('1%')
                        ->wrapHeader()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                    Tables\Columns\TextInputColumn::make('km_cadastro')
                        ->label('Km Cadastro')
                        ->wrapHeader()
                        ->width('1%')
                        ->type('number')
                        ->sortable()
                        ->disabled(fn(Viagem $record) => $record->conferido)
                        ->rules(['numeric', 'min:0', 'required'])
                        ->toggleable(isToggledHiddenByDefault: false)
                        ->afterStateUpdated(fn($state, Viagem $record) => (new IntegradoService)->atualizarKmRota(
                            Integrado::find($record->carga->integrado_id),
                            $state
                        )),
                    Tables\Columns\TextColumn::make('km_divergencia')
                        ->label('Km Divergência')
                        ->width('1%')
                        ->color(fn($state, Viagem $record): string => $record->km_divergencia > 3.49 ? 'danger' : 'info')
                        ->badge()
                        ->wrapHeader()
                        ->sortable()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('dispersao_percentual')
                        ->label('Dispersão %')
                        ->width('1%')
                        ->prefix('%')
                        ->badge()
                        ->wrapHeader()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextInputColumn::make('km_cobrar')
                        ->width('1%')
                        ->wrapHeader()
                        ->type('number')
                        ->disabled(fn(Viagem $record) => ($record->conferido && !Auth::user()->is_admin))
                        ->rules(['numeric', 'min:0', 'required'])
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('km_rota_corrigido')
                        ->wrapHeader()
                        ->width('1%')
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\SelectColumn::make('motivo_divergencia')
                        ->label('Motivo Divergência')
                        ->wrapHeader()
                        ->width('2%')
                        ->options(MotivoDivergenciaViagem::toSelectArray())
                        ->default(MotivoDivergenciaViagem::SEM_OBS->value)
                        ->disabled(fn(Viagem $record) => ($record->conferido && !Auth::user()->is_admin))
                        ->toggleable(isToggledHiddenByDefault: false)
                ]),
                Tables\Columns\ColumnGroup::make('Datas', [
                    Tables\Columns\TextInputColumn::make('data_competencia')
                        ->type('date')
                        ->label('Dt. Comp.')
                        ->width('1%')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('data_inicio')
                        ->label('Dt. Início')
                        ->width('1%')
                        ->dateTime('d/m/Y H:i')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('data_fim')
                        ->label('Dt. Fim')
                        ->width('1%')
                        ->dateTime('d/m/Y H:i')
                        ->dateTimeTooltip()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                Tables\Columns\IconColumn::make('conferido')
                    ->width('1%')
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'blue',
                        default => 'red',
                    }),
                Tables\Columns\TextColumn::make('complementos_count')
                    ->label('Complementos')
                    ->width('1%')
                    ->counts('complementos')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColumnGroup::make('Users', [
                    Tables\Columns\TextColumn::make('creator.name')
                        ->label('Criado Por')
                        ->width('1%')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('updater.name')
                        ->label('Atualizado Por')
                        ->width('1%')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('checker.name')
                        ->label('Conferido Por')
                        ->width('1%')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
            ])
            ->groups(
                [
                    Tables\Grouping\Group::make('data_competencia')
                        ->label('Data Competência')
                        ->titlePrefixedWithLabel(false)
                        ->getTitleFromRecordUsing(fn(Viagem $record): string => Carbon::parse($record->data_competencia)->format('d/m/Y'))
                        ->collapsible(),
                    Tables\Grouping\Group::make('veiculo.placa')
                        ->label('Veículo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                ]
            )
            ->defaultGroup('data_competencia')
            ->defaultSort('numero_viagem')
            ->searchOnBlur()
            ->deferFilters()
            ->persistFiltersInSession()
            ->filters([
                Tables\Filters\TernaryFilter::make('conferido')
                    ->label('Conferido')
                    ->trueLabel('Sim')
                    ->falseLabel('Não'),
                Tables\Filters\SelectFilter::make('integrado_id')
                    ->label('Integrado')
                    ->relationship('cargas.integrado', 'nome')
                    ->searchable(['codigo', 'nome'])
                    ->getOptionLabelFromRecordUsing(fn(Integrado $record) => "{$record->codigo} {$record->nome}")
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\Filter::make('numero_viagem')
                    ->form([
                        Forms\Components\TextInput::make('numero_viagem')
                            ->label('Nº Viagem'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['numero_viagem'],
                                fn(Builder $query, $numeroViagem): Builder => $query->where('numero_viagem', $numeroViagem),
                            );
                    }),
                Tables\Filters\SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->columnSpanFull(),
                Tables\Filters\Filter::make('data_competencia')
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio')
                            ->label('Data Comp. Início'),
                        Forms\Components\DatePicker::make('data_fim')
                            ->label('Data Comp. Fim'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_competencia', '>=', $date),
                            )
                            ->when(
                                $data['data_fim'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_competencia', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('motivo_divergencia')
                    ->label('Motivo Divergência')
                    ->options(MotivoDivergenciaViagem::toSelectArray())
                    ->multiple()
                    ->columnSpanFull(),
            ])
            ->deselectAllRecordsWhenFiltered(false)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('atualizar')
                        ->label('Atualizar')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Viagem $record) {}),
                    Tables\Actions\EditAction::make()
                        ->visible(fn(Viagem $record) => ! $record->conferido)
                        ->after(fn(Viagem $record) => (new \App\Services\ViagemService())->recalcularViagem($record)),
                    Tables\Actions\Action::make('importar-viagem')
                        ->tooltip('Alt. Dt. Próxima Viagem')
                        ->icon('heroicon-o-arrow-left-end-on-rectangle')
                        ->action(function (Viagem $record) {
                            $data = $record->data_competencia;
                            $veiculo_id = $record->veiculo_id;

                            $viagem = Viagem::query()
                                ->whereDate('data_competencia', '>', $data)
                                ->where('veiculo_id', $veiculo_id)
                                ->orderBy('data_fim', 'asc')
                                ->first();

                            if (! $viagem) {
                                notify::error('Nenhuma viagem encontrada', 'Não há viagens futuras para este veículo.');
                                return;
                            }

                            $viagem->data_competencia = $data;
                            $viagem->updated_by = Auth::user()->id;
                            $viagem->save();

                            notify::success('Viagem atualizada com sucesso!', 'A data da próxima viagem foi atualizada.');
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->link(),
                Tables\Actions\Action::make('nova-carga')
                    ->label('Carga')
                    ->icon('heroicon-o-plus')
                    ->modalSubmitAction(fn(\Filament\Actions\StaticAction $action) => $action->label('Adicionar Carga'))
                    ->form([
                        Forms\Components\Select::make('integrado_id')
                            ->label('Integrado')
                            ->relationship('carga.integrado', 'nome')
                            ->searchable(['codigo', 'nome'])
                            ->getOptionLabelFromRecordUsing(fn(Integrado $record) => "{$record->codigo} {$record->nome}")
                            ->required(),
                    ])
                    ->action(fn(Viagem $record, array $data) => CargaService::incluirCargaViagem($data['integrado_id'], $record))
                    ->after(fn() => notify::success('Carga incluída com sucesso!', 'A carga foi adicionada à viagem.')),
                Tables\Actions\Action::make('conferido')
                    ->label('Conferido')
                    ->iconButton()
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(Viagem $record) => ! $record->conferido)
                    ->action(function (Viagem $record) {
                        if (! $record->motivo_divergencia) {
                            $record->motivo_divergencia = MotivoDivergenciaViagem::SEM_OBS;
                        }
                        $record->conferido = true;
                        $record->updated_by = Auth::user()->id;
                        $record->checked_by = Auth::user()->id;
                        $record->save();
                        notify::success('Viagem conferida com sucesso!', 'A viagem foi marcada como conferida.');
                    }),
                Tables\Actions\Action::make('nao-conferido')
                    ->label('Ñ Conferido')
                    ->iconButton()
                    ->icon('heroicon-o-no-symbol')
                    ->color('red')
                    ->visible(fn(Viagem $record) => $record->conferido)
                    ->action(function (Viagem $record) {
                        $record->update([
                            'conferido' => false,
                            'updated_by' => Auth::user()->id,
                            'checked_by' => null,
                        ]);
                    }),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->is_admin),
                ]),
                Tables\Actions\BulkAction::make('conferido')
                    ->label('Conferir')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Collection $records) {
                        $records->each(function (Viagem $record) {
                            $record->conferido = true;
                            $record->updated_by = Auth::user()->id;
                            $record->checked_by = Auth::user()->id;
                            $record->save();
                        });
                    })
                    ->requiresConfirmation(),
                Tables\Actions\BulkAction::make('cobrar')
                    ->label('Cobrar')
                    ->icon('heroicon-o-banknotes')
                    ->action(function (Collection $records) {
                        $records->each(function (Viagem $record) {
                            if ($record->km_cobrar > 0) {
                                (new ViagemComplementoService)->create($record);
                            }
                        });
                    })
                    ->after(fn() => notify::success('Viagem registrada para cobrança!'))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(6)
            ->schema([
                \Filament\Infolists\Components\TextEntry::make('numero_viagem')
                    ->label('Nº Viagem'),
                \Filament\Infolists\Components\TextEntry::make('documento_transporte')
                    ->label('Doc. Transp.'),
                \Filament\Infolists\Components\TextEntry::make('data_competencia')
                    ->label('Dt. Comp.'),
                \Filament\Infolists\Components\TextEntry::make('veiculo.placa')
                    ->label('Placa'),
                \Filament\Infolists\Components\TextEntry::make('documentos.placa')
                    ->label('Placa'),
                \Filament\Infolists\Components\KeyValueEntry::make('divergencias')
                    ->label('Divergências')
                    ->keyLabel('Motivo')
                    ->valueLabel('Descrição')
                    ->columnSpanFull(),
            ]);
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
            'index' => Pages\ListViagems::route('/'),
            'create' => Pages\CreateViagem::route('/create'),
            // 'edit' => Pages\EditViagem::route('/{record}/edit'),
            'teste' => Pages\Teste::route('/{record}/teste'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AdvancedStatsOverviewWidget::class,
        ];
    }

    // Método para limpar cache do defaultGroup
    public static function clearDefaultGroupCache(?int $userId = null): void
    {
        $userId = $userId ?? Auth::user()->id;
        Cache::forget("defaultGroup-viagens-user-{$userId}");
    }

    // Método para forçar um defaultGroup específico
    public static function setUserDefaultGroup(string $group, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::user()->id;
        Cache::put("defaultGroup-viagens-user-{$userId}", $group, 1800);
    }
}
