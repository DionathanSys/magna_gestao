<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Enum\OrdemServico\TipoManutencaoEnum;
use App\Filament\Resources\OrdemServicoResource\Pages;
use App\Filament\Resources\OrdemServicoResource\RelationManagers;
use App\Filament\Resources\OrdemServicoResource\RelationManagers\ItensRelationManager;
use App\Models\OrdemServico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdemServicoResource extends Resource
{
    protected static ?string $model = OrdemServico::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Ordens de Serviço';

    protected static ?string $pluralLabel = 'Ordens de Serviço';

    protected static ?string $label = 'Ordem de Serviço';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                static::getVeiculoIdFormField(),
                static::getQuilometragemFormField(),
                static::getTipoManutencaoFormField(),
                static::getDataInicioFormField(),
                static::getDataFimFormField(),
                static::getStatusFormField(),
                static::getStatusSankhyaFormField(),
                static::getParceiroIdFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            ItensRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdemServicos::route('/'),
            'edit' => Pages\EditOrdemServico::route('/{record}/edit'),
        ];
    }

    public static function getVeiculoIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('veiculo_id')
            ->label('Veículo')
            ->required()
            ->relationship('veiculo', 'placa')
            ->searchable()
            ->preload();
    }

    public static function getQuilometragemFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('quilometragem')
            ->label('Quilometragem')
            ->numeric()
            ->minValue(0)
            ->maxValue(999999);
    }

    public static function getTipoManutencaoFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('tipo_manutencao')
            ->label('Tipo de Manutenção')
            ->options(TipoManutencaoEnum::toSelectArray())
            ->required()
            ->default(TipoManutencaoEnum::CORRETIVA->value);
    }

    public static function getDataInicioFormField(): Forms\Components\DateTimePicker
    {
        return Forms\Components\DateTimePicker::make('data_inicio')
            ->label('Dt. Inicio')
            ->seconds(false)
            ->required()
            ->maxDate(now())
            ->default(now());
    }

    public static function getDataFimFormField(): Forms\Components\DateTimePicker
    {
        return Forms\Components\DateTimePicker::make('data_fim')
            ->label('Dt. Fim')
            ->seconds(false)
            ->maxDate(now());
    }

    public static function getStatusFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status')
            ->label('Status')
            ->options(StatusOrdemServicoEnum::toSelectArray())
            ->default(StatusOrdemServicoEnum::PENDENTE->value)
            ->required();
    }

    public static function getStatusSankhyaFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status_sankhya')
            ->label('Sankhya')
            ->options(StatusOrdemServicoEnum::toSelectArray())
            ->default(StatusOrdemServicoEnum::PENDENTE->value)
            ->required();
    }

    public static function getParceiroIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('parceiro_id')
            ->label('Parceiro')
            ->relationship('parceiro', 'nome')
            ->searchable()
            ->preload();
    }
}
