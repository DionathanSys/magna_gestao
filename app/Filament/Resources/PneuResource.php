<?php

namespace App\Filament\Resources;

use App\Enum\Pneu\LocalPneuEnum;
use App\Enum\Pneu\MotivoMovimentoPneuEnum;
use App\Enum\Pneu\StatusPneuEnum;
use App\Filament\Resources\PneuResource\Pages;
use App\Filament\Resources\PneuResource\RelationManagers;
use App\Models\Pneu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PneuResource extends Resource
{
    protected static ?string $model = Pneu::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Pneus';

    protected static ?string $pluralLabel = 'Pneus';

    protected static ?string $label = 'Pneu';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Forms\Components\TextInput::make('numero_fogo')
                    ->label('Nº de Fogo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('marca')
                    ->maxLength(255),
                Forms\Components\TextInput::make('modelo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('medida')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ciclo')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(3),
                Forms\Components\Select::make('desenho_pneu_id')
                    ->relationship('desenhoPneu', 'modelo'),
                Forms\Components\Select::make('status')
                    ->options(StatusPneuEnum::toSelectArray())
                    ->required(),
                Forms\Components\Select::make('local')
                    ->options(LocalPneuEnum::toSelectArray())
                    ->required(),
                Forms\Components\DatePicker::make('data_aquisicao')
                    ->label('Dt. Aquisição')
                    ->default(now())
                    ->maxDate(now())
                    ->required(),
                Forms\Components\TextInput::make('ciclo_vida')
                    ->label('Ciclo de Vida')
                    ->numeric()
                    ->default(0)
                    ->minValue(fn(Pneu $record) => $record->ciclo_vida ?? 0)
                    ->maxValue(3)
                    ->visibleOn('edit')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_fogo')
                    ->label('Nº de Fogo')
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marca')
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('modelo')
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('medida')
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desenhoPneu.medida')
                    ->label('Medida Sulco (mm)')
                    ->wrapHeader()
                    ->width('1%')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ciclo_vida')
                    ->label('Ciclo de Vida')
                    ->wrapHeader()
                    ->width('1%'),
                Tables\Columns\SelectColumn::make('status')
                    ->width('1%')
                    ->options(StatusPneuEnum::toSelectArray()),
                Tables\Columns\SelectColumn::make('local')
                    ->width('1%')
                    ->options(LocalPneuEnum::toSelectArray()),
                Tables\Columns\TextColumn::make('data_aquisicao')
                    ->label('Dt. Aquisição')
                    ->date('d/m/Y')
                    ->sortable(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\ReplicateAction::make()
                    ->icon('heroicon-o-document-duplicate')
                    ->iconButton()
                    ->fillForm(fn(Pneu $record) => [
                        'marca'             => $record->marca,
                        'modelo'            => $record->modelo,
                        'medida'            => $record->medida,
                        'data_aquisicao'    => $record->data_aquisicao,
                        'local'             => $record->local,
                        'status'            => $record->status,
                        'ciclo'             => $record->ciclo,
                    ])
                    ->form(fn(Forms\Form $form) => static::form($form)->columns(4))
                    ->excludeAttributes([
                        'id',
                        'numero_fogo',
                        'created_at',
                        'updated_at',
                    ]),
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
            'index' => Pages\ListPneus::route('/'),
            // 'create' => Pages\CreatePneu::route('/create'),
            // 'edit' => Pages\EditPneu::route('/{record}/edit'),
        ];
    }

    public static function getDataInicialOrdemFormField(): Forms\Components\DatePicker
    {
        return  Forms\Components\DatePicker::make('data_inicial')
            ->label('Dt. Inicial')
            ->date('d/m/Y')
            ->default(now())
            ->maxDate(now())
            ->required();
    }

    public static function getDataFinalOrdemFormField(): Forms\Components\DatePicker
    {
        return  Forms\Components\DatePicker::make('data_final')
            ->label('Dt. Final')
            ->date('d/m/Y')
            ->default(now())
            ->maxDate(now())
            ->required();
    }

    public static function getKmInicialOrdemFormField(): Forms\Components\TextInput
    {
        return  Forms\Components\TextInput::make('km_inicial')
            ->label('KM Inicial')
            ->numeric()
            ->required();
    }

    public static function getKmFinalOrdemFormField(): Forms\Components\TextInput
    {
        return  Forms\Components\TextInput::make('km_final')
            ->label('KM Final')
            ->numeric()
            ->required();
    }

    public static function getPneuDisponivelFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('pneu_id')
            ->label('Pneu')
            ->options(
                Pneu::query()
                    ->whereDoesntHave('veiculo')
                    ->pluck('numero_fogo', 'id')
            )
            ->searchable()
            ->required();
    }

    public static function getMotivoMovimentacaoFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('motivo')
            ->columnSpan(3)
            ->options(MotivoMovimentoPneuEnum::toSelectArray())
            ->required();
    }

    public static function getSulcoFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('sulco')
            ->columnSpan(1)
            ->numeric()
            ->maxValue(30)
            ->minValue(0);
    }

    public static function getObservacaoFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('observacao')
            ->label('Observação')
            ->columnSpanFull()
            ->maxLength(255);
    }
}
