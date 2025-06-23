<?php

namespace App\Livewire;

use App\Models\Shop\Product;
use App\Models\Viagem;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;


class ListTeste extends Component implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;


     public function table(Table $table): Table
    {
        return $table
            ->query(Viagem::query())

            ->columns([
                TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                // TextColumn::make('numero_viagem')
                //     ->label('Nº Viagem')
                //     ->width('1%')
                //     ->sortable(),
                TextColumn::make('documentos_sum_valor_total')
                    ->sum('documentos', 'valor_total')
                    ->label('Frete')
                    ->width('1%')
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Sum::make()->money('BRL', locale: 'pt-BR')),
                TextColumn::make('km_rodado')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                TextColumn::make('km_pago')
                    ->width('1%')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 2, locale: 'pt-BR')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2, locale: 'pt-BR')),
                ColumnGroup::make('Datas',[
                    TextColumn::make('data_competencia')
                        ->label('Dt. Comp.')
                        ->width('1%')
                        ->sortable(),
                    TextColumn::make('data_inicio')
                        ->label('Dt. Início')
                        ->width('1%')
                        ->dateTime('d/m/Y H:i')
                        ->sortable(),
                    TextColumn::make('data_fim')
                        ->label('Dt. Fim')
                        ->width('1%')
                        ->dateTime('d/m/Y H:i')
                        ->dateTimeTooltip()
                        ->sortable(),
                    TextColumn::make('motivo_divergencia')
                        ->label('Motivo Divergência')
                        ->wrapHeader(),
                ]),
            ])
            ->groups(
                [
                    Group::make('data_competencia')
                        ->label('Data Competência')
                        ->titlePrefixedWithLabel(false)
                        ->getTitleFromRecordUsing(fn (Viagem $record): string => Carbon::parse($record->data_competencia)->format('d/m/Y'))
                        ->collapsible(),
                    Group::make('veiculo.placa')
                        ->label('Veículo')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                    Group::make('carga.integrado.nome')
                        ->label('Integrado')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                ]
            )
            ->defaultGroup('veiculo.placa')
            ->groupsOnly()
            ->defaultSort('km_rodado', 'desc')
            ->searchOnBlur()
            ->persistFiltersInSession()
            ->filters([
                TernaryFilter::make('conferido')
                    ->label('Conferido')
                    ->trueLabel('Sim')
                    ->falseLabel('Não'),
                SelectFilter::make('integrado_id')
                    ->label('Integrado')
                    ->relationship('cargas.integrado', 'nome')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Filter::make('numero_viagem')
                    ->form([
                        TextInput::make('numero_viagem')
                            ->label('Nº Viagem'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['numero_viagem'],
                                fn (Builder $query, $numeroViagem): Builder => $query->where('numero_viagem', $numeroViagem),
                            );
                    }),
                SelectFilter::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->columnSpanFull(),
                Filter::make('data_competencia')
                    ->form([
                        DatePicker::make('data_inicio')
                            ->label('Data Comp. Início'),
                        DatePicker::make('data_fim')
                            ->label('Data Comp. Fim'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_competencia', '>=', $date),
                            )
                            ->when(
                                $data['data_fim'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_competencia', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.list-teste');
    }
}
