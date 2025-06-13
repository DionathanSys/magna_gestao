<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViagemResource\Pages;
use App\Filament\Resources\ViagemResource\RelationManagers;
use App\Models\CargaViagem;
use App\Models\Integrado;
use App\Models\Viagem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->preload(),
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
                            ->readOnly(),
                        Forms\Components\TextInput::make('valor_cte')
                            ->readOnly(),
                        Forms\Components\TextInput::make('valor_nfs')
                            ->readOnly(),
                        Forms\Components\TextInput::make('valor_icms')
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
                        Forms\Components\TextInput::make('km_morto')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_cobrar')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('km_rota_corrigido')
                            ->required()
                            ->numeric()
                            ->default(0),
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
                // Forms\Components\KeyValue::make('divergencias')
                //     ->columnSpanFull(),
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
                    ->copyable()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('cargas.integrado.nome')
                    ->listWithLineBreaks()
                    ->width('1%')
                    ->label('Integrado')
                    ->url(fn (Viagem $record) => IntegradoResource::getUrl('edit', ['record' => $record->carga->integrado_id]))
                    ->openUrlInNewTab()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('numero_custo_frete')
                    ->label('Nº Custo Frete')
                    ->copyable()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->label('Doc. Transp.')
                    ->copyable()
                    ->sortable()
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
                        ->color(fn($state, Viagem $record): string => $record->km_cadastro != $record->km_pago ? 'info' : 'success')
                        ->badge()
                        ->wrapHeader()
                        ->width('1%')
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('km_divergencia')
                        ->label('Km Divergência')
                        ->color(fn($state, Viagem $record) => $record->km_divergencia > 0 ? 'info' : 'success')
                        ->badge()
                        ->wrapHeader()
                        ->width('1%')
                        ->sortable()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('km_pago_excedente')
                        ->wrapHeader()
                        ->width('1%')
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('km_morto')
                        ->width('1%')
                        ->wrapHeader()
                        ->sortable()
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
                ]),
                Tables\Columns\TextColumn::make('documentos_exists')
                    ->exists('documentos')
                    ->width('1%')
                    ->label('Doc. Frete'),
                Tables\Columns\TextColumn::make('documentos_sum_valor_total')
                    ->sum('documentos', 'valor_total')
                    ->width('1%')
                    ->money('BRL', locale: 'pt-BR')
                    ->label('Vlr. Frete'),
                Tables\Columns\TextColumn::make('cargas_count')
                    ->counts('cargas')
                    ->width('1%')
                    ->label('Qtde. Cargas')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('data_competencia')
                    ->width('1%')
                    ->label('Dt. Comp.')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->width('1%')
                    ->label('Dt. Início')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_fim')
                    ->width('1%')
                    ->label('Dt. Fim')
                    ->dateTime('d/m/Y H:i')
                    ->dateTimeTooltip()
                    ->sortable(),
                Tables\Columns\IconColumn::make('conferido'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
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
            ->defaultSort('numero_viagem')
            ->searchOnBlur()
            ->persistFiltersInSession()
            ->filters([
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
                Tables\Actions\Action::make('nova-carga')
                    ->label('Carga')
                    ->icon('heroicon-o-shopping-bag')
                    ->iconButton()
                    ->form([
                        Forms\Components\Select::make('integrado_id')
                            ->label('Integrado')
                            ->options(fn() => Integrado::all()->pluck('nome', 'id'))
                            ->required(),
                    ]),
                Tables\Actions\EditAction::make()
                    ->iconButton(),

            ])
            // ->selectable()
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
