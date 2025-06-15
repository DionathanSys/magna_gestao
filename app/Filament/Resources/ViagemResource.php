<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViagemResource\Pages;
use App\Filament\Resources\ViagemResource\RelationManagers;
use App\Models\CargaViagem;
use App\Models\Integrado;
use App\Models\Viagem;
use App\Enum\MotivoDivergenciaViagem;
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
use Illuminate\Support\Facades\Log;

class ViagemResource extends Resource
{
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
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('numero_viagem')
                    ->label('Nº Viagem')
                    ->width('1%')
                    ->sortable()
                    ->copyable()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('cargas.integrado.id')
                    ->label('ID Carga')
                    ->width('1%')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('cargas.integrado.nome')
                    ->label('Integrado')
                    ->width('1%')
                    ->listWithLineBreaks()
                    ->url(fn (Viagem $record) => IntegradoResource::getUrl('edit', ['record' => $record->carga->integrado_id ?? 0]))
                    ->openUrlInNewTab()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('numero_custo_frete')
                    ->label('Nº Custo Frete')
                    ->sortable()
                    ->copyable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->label('Doc. Transp.')
                    ->sortable()
                    ->copyable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipo_viagem')
                    ->searchable()
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
                    Tables\Columns\TextColumn::make('km_cadastro')
                        ->label('Km Cadastro')
                        ->color(fn($state, Viagem $record): string => $record->km_cadastro != $record->km_pago ? 'info' : '')
                        ->badge(fn($state, Viagem $record): bool => $record->km_cadastro != $record->km_pago)
                        ->wrapHeader()
                        ->width('1%')
                        ->sortable()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->toggleable(isToggledHiddenByDefault: false),
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
                    Tables\Columns\TextColumn::make('km_cobrar')
                        ->width('1%')
                        ->wrapHeader()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
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
                        ->default(MotivoDivergenciaViagem::DESLOCAMENTO_OUTROS->value)
                        ->disabled(fn(Viagem $record) => $record->conferido)
                ]),
                Tables\Columns\ColumnGroup::make('Datas',[
                    Tables\Columns\TextColumn::make('data_competencia')
                        ->label('Dt. Comp.')
                        ->width('1%')
                        ->date('d/m/Y')
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
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'blue',
                        default => 'red',
                    }),
                Tables\Columns\IconColumn::make('documentos_exists')
                    ->label('Doc. Existente')
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'blue',
                        default => 'red',
                    })
                    ->exists('documentos'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->groups(
                [
                    Tables\Grouping\Group::make('data_competencia')
                        ->label('Data Competência'),
                    Tables\Grouping\Group::make('veiculo.placa')
                        ->label('Veículo'),
                    Tables\Grouping\Group::make('carga.integrado.nome')
                        ->label('Integrado'),
                ]
            )
            ->defaultGroup('veiculo.placa')
            ->defaultSort('numero_viagem')
            ->searchOnBlur()
            ->persistFiltersInSession()
            ->filters([
                Tables\Filters\TernaryFilter::make('conferido')
                    ->label('Conferido')
                    ->trueLabel('Sim')
                    ->falseLabel('Não'),
                Tables\Filters\SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->columnSpanFull(),
                Tables\Filters\QueryBuilder::make()
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('data_inicio'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('data_fim'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('data_competencia'),
                    ]),


            ], layout: FiltersLayout::Modal)
            ->filtersFormWidth(MaxWidth::FourExtraLarge)
            ->filtersTriggerAction(
                fn(Tables\Actions\Action $action) => $action
                    ->button()
                    ->slideOver()
                    ->label('Filtros')
            )
            ->deselectAllRecordsWhenFiltered(false)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->openUrlInNewTab()
                        ->iconButton(),
                    Tables\Actions\Action::make('importar-viagem')
                        ->tooltip('Alt. Dt. Próxima Viagem')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->action(function(Viagem $record) {
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
                            $viagem->save();

                        }),
                    Tables\Actions\Action::make('nova-carga')
                        ->label('Carga')
                        ->icon('heroicon-o-plus')
                        ->form([
                            Forms\Components\Select::make('integrado_id')
                                ->label('Integrado')
                                ->options(fn() => Integrado::all()->pluck('nome', 'id'))
                                ->required(),
                        ]),
                ]),
                Tables\Actions\Action::make('conferido')
                    ->label('Viagem OK')
                    ->icon('heroicon-o-check')
                    ->visible(fn(Viagem $record) => ! $record->conferido)
                    ->action(function(Viagem $record) {
                        $record->update(['conferido' => true]);
                    }),
                Tables\Actions\Action::make('nao-conferido')
                    ->label('Viagem NOK')
                    ->icon('heroicon-o-no-symbol')
                    ->color('red')
                    ->visible(fn(Viagem $record) => $record->conferido)
                    ->action(function(Viagem $record) {
                        $record->update(['conferido' => false]);
                    }),
                Tables\Actions\Action::make('divergencias')
                    ->label('Divergências')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->fillForm(fn (Viagem $record): array => [
                        'divergencias' => $record->divergencias,
                    ])
                    ->form([
                        Forms\Components\KeyValue::make('divergencias')
                            ->columnSpanFull()
                        ])
                    ->action(fn(Viagem $record, array $data) => $record->update(['divergencias' => $data['divergencias']])),
                Tables\Actions\Action::make('km-cadastro')
                    ->label('KM')
                    ->icon('heroicon-o-pencil-square')
                    ->fillForm(fn (Viagem $record): array => [
                        'km_cadastro'       => $record->km_cadastro,
                        'km_rodado'         => $record->km_rodado,
                        'km_pago'           => $record->km_pago,
                        'km_rota_corrigido' => $record->km_rota_corrigido,
                    ])
                    ->form([
                        Forms\Components\TextInput::make('km_rodado')
                            ->label('KM Rodado')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('km_pago')
                            ->label('KM Pago')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('km_cadastro')
                            ->label('KM Cadastro')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('km_rota_corrigido')
                            ->label('KM Rota Corrigido')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function(Viagem $record, array $data) {

                            $record->update([
                                'km_cadastro'       => $data['km_cadastro'],
                                'km_rodado'         => $data['km_rodado'],
                                'km_pago'           => $data['km_pago'],
                                'km_rota_corrigido' => $data['km_rota_corrigido'],
                            ]);
                        })
                    ->after(fn(Viagem $record) => (new ViagemService())->recalcularViagem($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
        ];
    }
}
