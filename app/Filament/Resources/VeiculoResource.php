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
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->columnSpan(1)
                    ->inline(false)
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('km_movimento')
                    ->label('KM Movimento')
                    ->visibleOn('edit')
                    ->columnSpan(1)
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('data_movimento')
                    ->label('Dt. Movimento')
                    ->visibleOn('edit')
                    ->columnSpan(1)
                    ->date()
                    ->default(now())
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    // ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
