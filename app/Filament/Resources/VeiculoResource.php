<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VeiculoResource\Pages;
use App\Filament\Resources\VeiculoResource\RelationManagers;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationGroup = 'Veículos';

    protected static ?string $pluralModelLabel = 'Veículos';

    protected static ?string $pluralLabel = 'Veículos';

    protected static ?string $label = 'Veículo';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Forms\Components\TextInput::make('placa')
                    ->columnSpan(1)
                    ->disabledOn('edit')
                    ->required(),
                Forms\Components\Select::make('filial')
                    ->columnSpan(2)
                    ->options([
                        'CATANDUVAS' => 'Catanduvas',
                        'CHAPECO'    => 'Chapecó',
                        'CONCORDIA'  => 'Concórdia',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->columnSpan(1)
                    ->inline(false)
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('marca')
                    ->label('Marca')
                    ->columnSpan(2)
                    ->maxLength(50)
                    ->placeholder('Marca do veículo'),
                Forms\Components\TextInput::make('modelo')
                    ->label('Modelo')
                    ->columnSpan(2)
                    ->maxLength(50)
                    ->placeholder('Modelo do veículo'),
                Forms\Components\TextInput::make('chassis')
                    ->label('Chassi')
                    ->columnSpan(2)
                    ->maxLength(50)
                    ->placeholder('Chassi do veículo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('filial')
                    ->label('Filial'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('marca')
                    ->label('Marca')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('modelo')
                    ->label('Modelo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('chassis')
                    ->label('Chassi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado Em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado Em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Excluído Em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->persistFiltersInSession()
            ->filters([
                Tables\Filters\SelectFilter::make('filial')
                    ->options([
                        'CATANDUVAS' => 'Catanduvas',
                        'CHAPECO'    => 'Chapecó',
                        'CONCORDIA'  => 'Concórdia',
                    ])
                    ->default(fn() => Auth::user()->name == 'Carol' ? 'CATANDUVAS' : 'CHAPECO')
                    ->selectablePlaceholder(false),
            ])
            ->paginated([25, 50, 100])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        unset($data['km_movimento']);
                        unset($data['data_movimento']);
                        return $data;
                    }),
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
            RelationManagers\PneusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVeiculos::route('/'),
            'create' => Pages\CreateVeiculo::route('/create'),
            'edit' => Pages\EditVeiculo::route('/{record}/edit'),
        ];
    }
}
