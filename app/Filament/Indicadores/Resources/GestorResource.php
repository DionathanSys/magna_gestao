<?php

namespace App\Filament\Indicadores\Resources;

use App\Filament\Indicadores\Resources\GestorResource\Pages;
use App\Filament\Indicadores\Resources\GestorResource\RelationManagers;
use App\Filament\Indicadores\Resources\GestorResource\RelationManagers\IndicadoresRelationManager;
use App\Filament\Indicadores\Resources\GestorResource\RelationManagers\ResultadosRelationManager;
use App\Models\Gestor;
use App\Models\Resultado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use LDAP\Result;

class GestorResource extends Resource
{
    protected static ?string $model = Gestor::class;

    protected static ?string $pluralModelLabel = 'Gestores';

    protected static ?string $pluralLabel = 'Gestores';

    protected static ?string $label = 'Gestor';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                static::getNomeFormField(),
                static::getUnidadeFormField(),
                static::getSetorFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('setor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pontuacao_obtida')
                    ->label('Pontuação obtida'),
                Tables\Columns\TextColumn::make('pontuacao_maxima')
                    ->label('Pontuação máxima'),
                Tables\Columns\TextColumn::make('pontuacao_total')
                    ->label('Pontuação máxima'),
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
            IndicadoresRelationManager::class,
            ResultadosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestors::route('/'),
            // 'create' => Pages\CreateGestor::route('/create'),
            'edit' => Pages\EditGestor::route('/{record}/edit'),
        ];
    }

    public static function getNomeFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('nome')
            ->label('Nome')
            ->autocomplete(false)
            ->columnSpan(2)
            ->required()
            ->maxLength(255);
    }

    public static function getUnidadeFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('unidade')
            ->label('Unidade')
            ->columnSpan(2)
            ->options([
                'CATANDUVAS' => 'CATANDUVAS',
                'CHAPECÓ'   => 'CHAPECÓ',
                'CONCÓRDIA' => 'CONCÓRDIA'
            ])
            ->required();
    }

    public static function getSetorFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('setor')
            ->label('Setor')
            ->autocomplete(false)
            ->columnSpan(2)
            ->required();
    }


}
