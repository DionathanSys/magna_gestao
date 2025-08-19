<?php

namespace App\Filament\Indicadores\Resources;

use App\Filament\Indicadores\Resources\ResultadoResource\Pages;
use App\Filament\Indicadores\Resources\ResultadoResource\RelationManagers;
use App\Models\Resultado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Leandrocfe\FilamentPtbrFormFields\Money;

class ResultadoResource extends Resource
{
    protected static ?string $model = Resultado::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                static::getGestorIdFormField(),
                static::getIndicadorIdFormField(),
                static::getStatusFormField(),
                static::getObjetivoFormField(),
                static::getResultadoFormField(),
                static::getPeriodoFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gestor.nome')
                    ->label('Gestor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.descricao')
                    ->label('Indicador')
                    ->icon(fn($record) => match ($record->indicador->tipo_avaliacao) {
                        'maior_melhor' => 'heroicon-s-arrow-up-circle',
                        'menor_melhor' => 'heroicon-s-arrow-down-circle',
                    })
                    ->iconColor('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('objetivo')
                    ->label('Meta')
                    ->formatStateUsing(function($record, $state) {
                        return match($record->indicador->tipo_meta) {
                            '%' => number_format($state, 2, ',', '.') . '%',
                            'R$' => 'R$ ' . number_format($state, 2, ',', '.'),
                            default => $state,
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('resultado')
                    ->label('Resultado')
                    ->formatStateUsing(function($record, $state) {
                        return match($record->indicador->tipo_meta) {
                            '%' => number_format($state, 2, ',', '.') . '%',
                            'R$' => 'R$ ' . number_format($state, 2, ',', '.'),
                            default => $state,
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('pontuacao_obtida')
                    ->label('Pontuação')
                    ->numeric('3', ',', '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.peso_por_periodo')
                    ->label('% Obtido')
                    ->formatStateUsing(fn($record, $state) => number_format(($record->pontuacao_obtida / $state) * 100, 2, ',', '.'))
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(fn($record) => match($record->status) {
                        'n_atingido' => 'Não Atingido',
                        'parcialmente_atingido' => 'Parcialmente Atingido',
                        'atingido' => 'Atingido',
                    })
                    ->color(fn($record) => match($record->status) {
                        'n_atingido' => 'danger',
                        'parcialmente_atingido' => 'warning',
                        'atingido' => 'info',
                    })
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodo')
                    ->label('Período')
                    ->dateTime('F/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('gestor.nome')
                    ->label('Gestor')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
                Group::make('indicador.descricao')
                    ->label('Indicador')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
                Group::make('periodo')
                    ->label('Período')
                    ->date('F/Y')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),

            ])
            ->defaultGroup('gestor.nome')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListResultados::route('/'),
            'create' => Pages\CreateResultado::route('/create'),
            'edit' => Pages\EditResultado::route('/{record}/edit'),
        ];
    }

    public static function getIndicadorIdFormField(): Forms\Components\Select
    {
        //TODO Limitar opções
        return Forms\Components\Select::make('indicador_id')
            ->label('Indicador')
            ->columnSpan(4)
            ->relationship('indicador', 'descricao')
            ->required()
            ->preload()
            ->searchable();
    }

    public static function getGestorIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('gestor_id')
            ->label('Gestor')
            ->columnSpan(4)
            ->relationship('gestor', 'nome')
            ->preload()
            ->searchable();
    }

    public static function getPontuacaoObtidaFormField(): Money
    {
        return Money::make('pontuacao_obtida')
            ->label('Pontuação Obtida')
            ->columnSpan(2)
            ->prefix(null)
            ->required()
            ->minValue(0);
    }

    public static function getPontuacaoMaximaFormField(): Money
    {
        return Money::make('pontuacao_maxima')
            ->label('Pontuação Máxima')
            ->columnSpan(2)
            ->prefix(null)
            ->required()
            ->minValue(0);
    }
    public static function getObjetivoFormField(): Money
    {
        return Money::make('objetivo')
            ->label('Meta')
            ->columnSpan(2)
            ->prefix(fn($record) => $record->indicador->tipo_meta ?? null)
            ->required()
            ->reactive()
            ->minValue(0);
    }
    public static function getResultadoFormField(): Money
    {
        return Money::make('resultado')
            ->label('Resultado')
            ->columnSpan(2)
            ->prefix(fn($record) => $record->indicador->tipo_meta ?? null)
            ->required()
            ->reactive()
            ->minValue(0);
    }

    public static function getStatusFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status')
            ->label('Status')
            ->columnSpan(2)
            ->visibleOn('edit')
            ->disabled()
            ->options([
                'n_atingido' => 'Não Atingido',
                'parcialmente_atingido' => 'Parcialmente Atingido',
                'atingido' => 'Atingido',
            ])
            ->default('n_atingido')
            ->required();
    }

    public static function getPeriodoFormField(): Forms\Components\DatePicker
    {
        return Forms\Components\DatePicker::make('periodo')
            ->label('Período')
            ->placeholder(now()->startOfMonth())
            ->default(now()->startOfMonth())
            ->columnSpan(2)
            ->required();
    }

}
