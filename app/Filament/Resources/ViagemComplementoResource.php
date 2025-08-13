<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViagemComplementoResource\Pages;
use App\Filament\Resources\ViagemComplementoResource\RelationManagers;
use App\Models\Integrado;
use App\Models\ViagemComplemento;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ViagemComplementoResource extends Resource
{
    protected static ?string $model = ViagemComplemento::class;

     protected static ?string $navigationGroup = 'Viagens';

    protected static ?string $pluralModelLabel = 'Complemento Viagens';

    protected static ?string $pluralLabel = 'Complemento Viagens';

    protected static ?string $label = 'Complemento Viagem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric(0,'','')
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('numero_viagem')
                    ->label('Nº Viagem')
                    ->numeric(0,'','')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('documento_transporte')
                    ->numeric(0,'','')
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('integrado.codigo')
                    ->numeric(0,'','')
                    ->sortable(),
                Tables\Columns\TextColumn::make('integrado.nome')
                    ->label('Integrado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_rodado')
                    ->numeric(2, ',','.')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_pago')
                    ->numeric(2, ',','.')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_divergencia')
                    ->numeric(2, ',','.')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('km_cobrar')
                    ->numeric(2, ',','.')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('motivo_divergencia')
                    ->label('Motivo Divergência')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_competencia')
                    ->label('Data Competência')
                    ->date('d/m/Y')
                    ->searchable(),
                Tables\Columns\IconColumn::make('conferido')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
                    Tables\Grouping\Group::make('data_competencia')
                        ->label('Data Competência')
                        ->titlePrefixedWithLabel(false)
                        ->getTitleFromRecordUsing(fn(ViagemComplemento $record): string => Carbon::parse($record->data_competencia)->format('d/m/Y'))
                        ->collapsible(),
                    Tables\Grouping\Group::make('integrado.nome')
                        ->label('Integrado')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                    Tables\Grouping\Group::make('veiculo.placa')
                        ->label('Veículo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                    Tables\Grouping\Group::make('numero_viagem')
                        ->label('Nº Viagem')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                ]
            )
            ->defaultGroup('numero_viagem')
            ->deferFilters()
            ->searchOnBlur()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->filters([
                Tables\Filters\SelectFilter::make('integrado_id')
                    ->label('Integrado')
                    ->relationship('integrado', 'nome')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
                Tables\Actions\BulkAction::make('conferido')
                        ->label('Conferir')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            $records->each(function (ViagemComplemento $record) {
                                $record->conferido = true;
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListViagemComplementos::route('/'),
            'create' => Pages\CreateViagemComplemento::route('/create'),
            'edit' => Pages\EditViagemComplemento::route('/{record}/edit'),
        ];
    }
}
