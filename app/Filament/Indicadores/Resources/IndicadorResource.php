<?php

namespace App\Filament\Indicadores\Resources;

use App\Filament\Indicadores\Resources\IndicadorResource\Pages;
use App\Filament\Indicadores\Resources\IndicadorResource\RelationManagers;
use App\Filament\Indicadores\Resources\IndicadorResource\RelationManagers\GestoresRelationManager;
use App\Models\Indicador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IndicadorResource extends Resource
{
    protected static ?string $model = Indicador::class;

    protected static ?string $pluralModelLabel = 'Indicadores';

    protected static ?string $pluralLabel = 'Indicadores';

    protected static ?string $label = 'Indicador';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                static::getDescricaoFormField(),
                static::getTipoAvalicaoFormField(),
                static::getTipoValorMetaFormField(),
                static::getPesoFormField(),
                static::getPeriodicidadeFormField(),
                static::getTipoFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Indicador')
                    ->icon(fn($record) => match ($record->tipo_avaliacao) {
                        'maior_melhor' => 'heroicon-o-arrow-trending-up',
                        'menor_melhor' => 'heroicon-o-arrow-trending-down',
                    })
                    ->iconPosition(IconPosition::After)
                    ->color(fn($record) => match ($record->tipo_avaliacao) {
                        'maior_melhor' => 'info',
                        'menor_melhor' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('peso')
                    ->numeric('2', ',' , '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodicidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo'),
                Tables\Columns\TextColumn::make('tipo_meta')
                    ->label('Tipo Resultado')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipo_avaliacao')
                    ->label('Tipo de Avaliação')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Excluído em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            GestoresRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIndicadors::route('/'),
            'edit' => Pages\EditIndicador::route('/{record}/edit'),
        ];
    }

    public static function getDescricaoFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('descricao')
            ->label('Descrição')
            ->columnSpan(4)
            ->autocomplete(false)
            ->required()
            ->maxLength(255);
    }

    public static function getTipoAvalicaoFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('tipo_avaliacao')
            ->label('Tipo de Avaliação')
            ->columnSpan(2)
            ->native(false)
            ->required()
            ->options([
                'maior_melhor'  => 'Maior',
                'menor_melhor' => 'Menor',
            ])
            ->searchPrompt('')
            ->placeholder('')
            ->default('%');
    }

    public static function getTipoValorMetaFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('tipo_meta')
            ->columnSpan(2)
            ->native(false)
            ->required()
            ->options([
                'R$'  => 'R$',
                '%' => '%',
            ])
            ->searchPrompt('')
            ->placeholder('')
            ->default('%');
    }

    public static function getPesoFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('peso')
            ->columnSpan(2)
            ->autocomplete(false)
            ->required()
            ->numeric()
            ->default(0);
    }

    public static function getPeriodicidadeFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('periodicidade')
            ->columnSpan(2)
            ->required()
            ->options([
                'MENSAL'        => 'Mensal',
                'TRIMESTRAL'    => 'Trimestral',
                'SEMESTRAL'     => 'Semestral',
                'ANUAL'         => 'Anual',
            ])
            ->default('MENSAL');
    }

    public static function getTipoFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('tipo')
            ->columnSpan(2)
            ->options([
                'COLETIVO'      => 'Coletivo',
                'INDIVIDUAL'    => 'Individual',
            ])
            ->default('INDIVIDUAL')
            ->required();
    }
}
