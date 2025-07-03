<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViagemResource\Pages;
use App\Filament\Resources\ViagemResource\RelationManagers;
use App\Models\CargaViagem;
use App\Models\Integrado;
use App\Models\Viagem;
use App\Enum\MotivoDivergenciaViagem;
use App\Filament\Resources\ViagemResource\Widgets\AdvancedStatsOverviewWidget;
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
                Forms\Components\Section::make('Frete')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('valor_frete')
                            ->prefix('R$')
                            ->readOnly(),
                        Forms\Components\TextInput::make('valor_cte')
                            ->prefix('R$')
                            ->readOnly(),
                        Forms\Components\TextInput::make('valor_nfs')
                            ->prefix('R$')
                            ->readOnly(),
                        Forms\Components\TextInput::make('valor_icms')
                            ->prefix('R$')
                            ->readOnly(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('cargas.integrado.nome')
                    ->label('Integrado')
                    ->width('1%')
                    ->tooltip(fn(Viagem $record) => $record->carga->integrado?->codigo ?? 'N/A')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->label('Doc. Transp.')
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
                    Tables\Columns\TextColumn::make('km_rodado_excedente')
                        ->label('Km Perdido')
                        ->width('1%')
                        ->color(fn($state, Viagem $record): string => $record->km_rodado_excedente > 0 ? 'info' : '')
                        ->badge(fn($state, Viagem $record): bool => $record->km_rodado_excedente > 0)
                        ->wrapHeader()
                        ->sortable()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('km_pago_excedente')
                        ->wrapHeader()
                        ->width('1%')
                        ->color(fn($state, Viagem $record): string => $record->km_pago_excedente > 0 ? 'info' : '')
                        ->badge(fn($state, Viagem $record): bool => $record->km_pago_excedente > 0)
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextInputColumn::make('km_cobrar')
                        ->width('1%')
                        ->wrapHeader()
                        ->type('number')
                        ->disabled(fn(Viagem $record) => $record->conferido)
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
                        ->disabled(fn(Viagem $record) => $record->conferido)
                ]),
                Tables\Columns\ColumnGroup::make('Datas', [
                    Tables\Columns\TextInputColumn::make('data_competencia')
                        ->type('date')
                        ->label('Dt. Comp.')
                        ->width('1%')
                        ->disabled(fn(Viagem $record) => $record->conferido)
                        ->sortable(),
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
                        ->sortable(),
                ]),
                Tables\Columns\IconColumn::make('conferido')
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'blue',
                        default => 'red',
                    }),
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
            ->persistFiltersInSession()
            ->filters([
                Tables\Filters\TernaryFilter::make('conferido')
                    ->label('Conferido')
                    ->trueLabel('Sim')
                    ->falseLabel('Não'),
                Tables\Filters\SelectFilter::make('integrado_id')
                    ->label('Integrado')
                    ->relationship('cargas.integrado', 'nome')
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
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('atualizar')
                        ->label('Atualizar')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Viagem $record) {}),
                    Tables\Actions\Action::make('editar')
                        ->url(fn(Viagem $record): string => ViagemResource::getUrl('edit', ['record' => $record->id]))
                        ->openUrlInNewTab()
                        ->visible(fn(Viagem $record) => ! $record->conferido)
                        ->icon('heroicon-o-pencil-square'),
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
                        }),
                ])
                    ->link(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\Action::make('nova-carga')
                    ->label('Carga')
                    ->icon('heroicon-o-plus')
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
                // Tables\Actions\Action::make('km-cadastro')
                //     ->label('KM')
                //     ->icon('heroicon-o-pencil-square')
                //     ->fillForm(fn (Viagem $record): array => [
                //         'km_cadastro'       => $record->km_cadastro,
                //         'km_rodado'         => $record->km_rodado,
                //         'km_pago'           => $record->km_pago,
                //         'km_rota_corrigido' => $record->km_rota_corrigido,
                //     ])
                //     ->form([
                //         Forms\Components\TextInput::make('km_rodado')
                //             ->label('KM Rodado')
                //             ->numeric()
                //             ->required(),
                //         Forms\Components\TextInput::make('km_pago')
                //             ->label('KM Pago')
                //             ->numeric()
                //             ->required(),
                //         Forms\Components\TextInput::make('km_cadastro')
                //             ->label('KM Cadastro')
                //             ->numeric()
                //             ->required(),
                //         Forms\Components\TextInput::make('km_rota_corrigido')
                //             ->label('KM Rota Corrigido')
                //             ->numeric()
                //             ->required(),
                //     ])
                //     ->action(function(Viagem $record, array $data) {

                //             $record->update([
                //                 'km_cadastro'       => $data['km_cadastro'],
                //                 'km_rodado'         => $data['km_rodado'],
                //                 'km_pago'           => $data['km_pago'],
                //                 'km_rota_corrigido' => $data['km_rota_corrigido'],
                //             ]);
                //         })
                //     ->after(fn(Viagem $record) => (new ViagemService())->recalcularViagem($record)),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('conferido')
                        ->label('Conferir')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            $records->each(function (Viagem $record) {
                                $record->conferido = true;
                                $record->save();
                            });
                        })
                        ->requiresConfirmation(),
                ]),
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
            'edit' => Pages\EditViagem::route('/{record}/edit'),
            'teste' => Pages\Teste::route('/{record}/teste'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AdvancedStatsOverviewWidget::class,
        ];
    }
}
