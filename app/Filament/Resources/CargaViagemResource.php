<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Enum\MotivoDivergenciaViagem;
use App\Filament\Resources\CargaViagemResource\Pages;
use App\Filament\Resources\CargaViagemResource\RelationManagers;
use App\Models\CargaViagem;
use App\Models\Integrado;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CargaViagemResource extends Resource
{
    protected static ?string $model = CargaViagem::class;

    protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Cargas';

    protected static ?string $pluralLabel = 'Cargas';

    protected static ?string $label = 'Carga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('viagem_id')
                    ->relationship('viagem', 'numero_viagem')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('integrado_id')
                    ->relationship('integrado', 'nome')
                    ->required(),
                Forms\Components\TextInput::make('documento_frete_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->with([
                    'viagem',
                    'viagem',
                    'integrado',
                ]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('viagem.veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->wrapHeader()
                    ->sortable(),
                Tables\Columns\TextColumn::make('viagem.data_competencia')
                    ->label('Dt. Comp.')
                    ->dateTime('d/m/Y')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('viagem.numero_viagem')
                    ->label('Nº Viagem')
                    ->width('1%')
                    ->numeric(0, '', '')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('integrado.codigo')
                    ->label('Cód. Integrado')
                    ->wrapHeader()
                    ->width('1%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('integrado.nome')
                    ->label('Integrado')
                    ->width('1%')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Doc. Frete')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColumnGroup::make('KM', [
                    Tables\Columns\TextColumn::make('viagem.km_rodado')
                        ->label('Km Rodado')
                        ->width('1%')
                        ->wrapHeader()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR'),
                    Tables\Columns\TextColumn::make('viagem.km_pago')
                        ->label('Km Pago')
                        ->width('1%')
                        ->wrapHeader()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR'),
                    Tables\Columns\TextColumn::make('viagem.km_cadastro')
                        ->label('Km Cadastro')
                        ->width('1%')
                        ->wrapHeader()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR'),
                    Tables\Columns\TextColumn::make('viagem.km_rodado_excedente')
                        ->label('Km Perdido')
                        ->width('1%')
                        ->color(fn($state, CargaViagem $record): string => $record->viagem->km_rodado_excedente > 0 ? 'info' : '')
                        ->badge(fn($state, CargaViagem $record): bool => $record->viagem->km_rodado_excedente > 0)
                        ->wrapHeader()
                        ->sortable()
                        ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('viagem.km_cobrar')
                        ->label('Km Cobrar')
                        ->width('1%')
                        ->wrapHeader()
                        ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('viagem.motivo_divergencia')
                        ->label('Motivo Divergência')
                        ->width('2%')
                        ->formatStateUsing(fn($state) => $state?->value ?? '')
                        ->wrapHeader(),
                    Tables\Columns\IconColumn::make('viagem.conferido')
                        ->label('Conferido')
                        ->boolean(),
                ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(
                [
                    Tables\Grouping\Group::make('viagem.numero_viagem')
                        ->label('Nº Viagem')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                    Tables\Grouping\Group::make('viagem.data_competencia')
                        ->label('Data Competência')
                        ->titlePrefixedWithLabel(false)
                        ->getTitleFromRecordUsing(fn(CargaViagem $record): string => Carbon::parse($record->data_competencia)->format('d/m/Y'))
                        ->collapsible(),
                    Tables\Grouping\Group::make('viagem.veiculo.placa')
                        ->label('Veículo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                    Tables\Grouping\Group::make('integrado.nome')
                        ->label('Integrado')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                    Tables\Grouping\Group::make('viagem.motivo_divergencia')
                        ->label('Motivo Divergência')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                ]
            )
            ->persistFiltersInSession()
            ->deferFilters()
            ->filters([
                Tables\Filters\SelectFilter::make('motivo_divergencia')
                    ->label('Motivo Divergência')
                    ->relationship('viagem', 'motivo_divergencia')
                    ->searchable()
                    ->preload()
                    // ->options(MotivoDivergenciaViagem::toSelectArray())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('viagem.veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('integrado_id')
                    ->label('Integrado')
                    ->options(Integrado::query()
                        ->orderBy('nome')
                        ->pluck('nome', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\Filter::make('data_competencia')
                    ->columns(6)
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio')
                            ->label('Data Comp. Início')
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('data_fim')
                            ->label('Data Comp. Fim')
                            ->columnSpan(2),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn(Builder $query, $date) =>
                                $query->whereHas('viagem', fn($q) => $q->whereDate('data_competencia', '>=', $date))
                            )
                            ->when(
                                $data['data_fim'],
                                fn(Builder $query, $date) =>
                                $query->whereHas('viagem', fn($q) => $q->whereDate('data_competencia', '<=', $date))
                            );
                    }),
                Tables\Filters\QueryBuilder::make()
                    ->constraints([
                            \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('integrado')
                                ->emptyable()
                                ->multiple()
                        ])
                        ], layout: FiltersLayout::AboveContentCollapsible)
            ->searchOnBlur()
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->fileName('Cargas Viagem')
                        ->disableAdditionalColumns()
                        ->pageOrientationFieldLabel('Page Orientation') // Label for page orientation input
                        ->filterColumnsFieldLabel('filter columns')
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
            'index' => Pages\ListCargaViagems::route('/'),
            // 'create' => Pages\CreateCargaViagem::route('/create'),
            // 'edit' => Pages\EditCargaViagem::route('/{record}/edit'),
        ];
    }
}
