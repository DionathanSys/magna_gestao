<?php

namespace App\Filament\Resources;

use App\Enum\TipoServicoEnum;
use App\Filament\Resources\PlanoPreventivoResource\Pages;
use App\Filament\Resources\PlanoPreventivoResource\RelationManagers;
use App\Filament\Resources\PlanoPreventivoResource\RelationManagers\VeiculosRelationManager;
use App\Models\PlanoPreventivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanoPreventivoResource extends Resource
{
    protected static ?string $model = PlanoPreventivo::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Planos Preventivos';

    protected static ?string $pluralLabel = 'Planos Preventivos';

    protected static ?string $label = 'Plano Preventivo';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(
                [
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 8,
                ])
            ->schema([
                Forms\Components\TextInput::make('descricao')
                    ->label('Descrição')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 2,
                    ])
                    ->maxLength(255),
                Forms\Components\TextInput::make('intervalo')
                    ->label('Intervalo')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 2,
                    ])
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 2,
                    ])
                    ->inline(false)
                    ->default(true),
                Forms\Components\Repeater::make('itens')
                    ->label('Itens do Plano')
                    // ->columnSpanFull()
                    ->columns([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 4,
                    ])
                    ->schema([
                        Forms\Components\Select::make('servico_id')
                            ->label('Descrição do Item')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 1,
                                'lg' => 2,
                            ])
                            ->options(
                                \App\Models\Servico::where('is_active', true)
                                    ->where('tipo', TipoServicoEnum::PREVENTIVA->value)
                                    ->orderBy('descricao')
                                    ->pluck('descricao', 'id')
                            )
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('periodicidade')
                    ->label('Periodicidade'),
                Tables\Columns\TextColumn::make('intervalo')
                    ->label('Intervalo'),
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
            VeiculosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanoPreventivos::route('/'),
            // 'create' => Pages\CreatePlanoPreventivo::route('/create'),
            'edit' => Pages\EditPlanoPreventivo::route('/{record}/edit'),
        ];
    }
}
