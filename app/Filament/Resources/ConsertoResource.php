<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsertoResource\Pages;
use App\Filament\Resources\ConsertoResource\RelationManagers;
use App\Models\Conserto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsertoResource extends Resource
{
    protected static ?string $model = Conserto::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Consertos';

    protected static ?string $pluralLabel = 'Consertos';

    protected static ?string $label = 'Conserto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                static::getPneuIdFormField(),
                static::getDataConsertoFormField(),
                static::getTipoConsertoFormField(),
                static::getParceiroIdFormField(),
                static::getValorConsertoFormField(),
                static::getGarantiaFormField(),
                static::getVeiculoIdFormField(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->label('Nº Fogo')
                    ->width('1%')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_conserto')
                    ->label('Dt. Reparo')
                    ->width('1%')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_conserto')
                    ->label('Tipo de Reparo')
                    ->width('1%')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->width('1%')
                    ->numeric()
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->width('1%')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\IconColumn::make('garantia')
                    ->label('Com Garantia')
                    ->width('1%')
                    ->boolean(),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->width('1%')
                    ->placeholder('Sem Veículo')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('garantia')
                    ->label('Sem Garantia')
                    ->toggle()
                    ->default(false)
                    ->query(fn (Builder $query): Builder => $query->where('garantia', false))

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListConsertos::route('/'),
            // 'create' => Pages\CreateConserto::route('/create'),
            // 'edit' => Pages\EditConserto::route('/{record}/edit'),
        ];
    }

    public static function getParceiroIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('parceiro_id')
            ->label('Parceiro')
            ->relationship('parceiro', 'nome')
            ->required();
    }

    public static function getValorConsertoFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('valor')
            ->label('Valor do Conserto')
            ->prefix('R$')
            ->numeric()
            ->default(0.00);
    }

    public static function getGarantiaFormField(): Forms\Components\Toggle
    {
        return Forms\Components\Toggle::make('garantia')
            ->label('Com Garantia')
            ->inline(false)
            ->default(true)
            ->required();
    }

    public static function getVeiculoIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('veiculo_id')
            ->label('Veículo')
            ->relationship('veiculo', 'placa');

    }

    public static function getTipoConsertoFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('tipo_conserto')
            ->options([
                'VULGANIZAÇÃO' => 'VULGANIZAÇÃO',
                'RAC' => 'RAC',
            ])
            ->default('VULGANIZAÇÃO')
            ->required();
    }

    public static function getDataConsertoFormField(): Forms\Components\DatePicker
    {
        return Forms\Components\DatePicker::make('data_conserto')
            ->label('Data do Conserto')
            ->date('d/m/Y')
            ->displayFormat('d/m/Y')
            // ->native(false)
            ->default(now())
            ->maxDate(now())
            ->closeOnDateSelection()
            ->required();
    }

    public static function getPneuIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('pneu_id')
            ->label('Pneu')
            ->relationship('pneu', 'numero_fogo')
            ->required();
    }
}
