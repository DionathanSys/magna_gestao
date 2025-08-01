<?php

namespace App\Filament\Resources;

use App\Enum\TipoServicoEnum;
use App\Filament\Resources\ServicoResource\Pages;
use App\Filament\Resources\ServicoResource\RelationManagers;
use App\Models\Servico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicoResource extends Resource
{
    protected static ?string $model = Servico::class;

    protected static ?string $navigationGroup = 'Manutenção';

    protected static ?string $pluralModelLabel = 'Serviços';

    protected static ?string $pluralLabel = 'Serviços';

    protected static ?string $label = 'Serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->columns([
                'sm' => 1,
                'md' => 4,
                'lg' => 8,
            ])
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 2
                    ])
                    ->maxLength(10),
                Forms\Components\TextInput::make('descricao')
                    ->label('Descrição')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 6
                    ])
                    ->maxLength(255),
                Forms\Components\TextInput::make('complemento')
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3
                    ])
                    ->options(TipoServicoEnum::toSelectArray())
                    ->default(TipoServicoEnum::CORRETIVA->value),
                Forms\Components\Toggle::make('controla_posicao')
                    ->label('Controla Posição')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3
                    ])
                    ->inline(false)
                    ->default(false)
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2
                    ])
                    ->inline(false)
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('complemento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(isIndividual: true),
                Tables\Columns\IconColumn::make('controla_posicao')
                    ->boolean(),
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
            ->persistFiltersInSession(true)
            ->actions([
                Tables\Actions\EditAction::make()
                // ->successNotification(null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    // ->successNotification(null),
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
            'index' => Pages\ListServicos::route('/'),
            'edit' => Pages\EditServico::route('/{record}/edit'),
        ];
    }
}
