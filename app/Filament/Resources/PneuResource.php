<?php

namespace App\Filament\Resources;

use App\Enum\Pneu\LocalPneuEnum;
use App\Enum\Pneu\MotivoMovimentoPneuEnum;
use App\Enum\Pneu\StatusPneuEnum;
use App\Filament\Resources\PneuResource\Pages;
use App\Filament\Resources\PneuResource\RelationManagers;
use App\Models\Pneu;
use App\Services\Pneus\ConsertoService;
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
                Forms\Components\Select::make('medida')
                    ->options([
                        '275/80 R22.5' => '275/80 R22.5',
                        '295/80 R22.5' => '295/80 R22.5',
                    ])
                    ->default('275/80 R22.5'),
                Forms\Components\TextInput::make('ciclo_vida')
                    ->label('Vida')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(3),
                Forms\Components\Select::make('desenho_pneu_id')
                    ->relationship('desenhoPneu', 'modelo'),
                Forms\Components\Select::make('status')
                    ->options(StatusPneuEnum::toSelectArray())
                    ->required()
                    ->default(StatusPneuEnum::DISPONIVEL->value),
                Forms\Components\Select::make('local')
                    ->options(LocalPneuEnum::toSelectArray())
                    ->required()
                    ->default(LocalPneuEnum::ESTOQUE_CCO->value),
                Forms\Components\DatePicker::make('data_aquisicao')
                    ->label('Dt. Aquisição')
                    ->default(now())
                    ->maxDate(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('numero_fogo')
                    ->label('Nº de Fogo')
                    ->width('1%')
                    ->numeric(null, '', '')
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
                    ->label('Vida')
                    ->wrapHeader()
                    ->width('1%'),
                Tables\Columns\SelectColumn::make('status')
                    ->width('1%')
                    ->options(StatusPneuEnum::toSelectArray()),
                Tables\Columns\SelectColumn::make('local')
                    ->options(LocalPneuEnum::toSelectArray())
                    ->width('1%'),
                Tables\Columns\TextColumn::make('ultimoRecap.desenhoPneu.descricao')
                    ->label('Borracha Recap Atual')
                    ->wrapHeader()
                    ->placeholder('N/A')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_aquisicao')
                    ->label('Dt. Aquisição')
                    ->date('d/m/Y')
                    ->sortable()
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
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('numero_fogo')
                    ->label('Nº de Fogo')
                    ->options(
                        Pneu::query()
                            ->pluck('numero_fogo', 'id')
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('estado_pneu')
                        ->options([
                            'NOVO'     => 'NOVO',
                            'RECAPADO' => 'RECAPADO',
                        ]),
                Tables\Filters\SelectFilter::make('marca')
                    ->options(
                        Pneu::query()
                            ->groupBy('marca')
                            ->pluck('marca', 'marca')
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('modelo')
                    ->options(
                        Pneu::query()
                            ->groupBy('modelo')
                            ->pluck('modelo', 'modelo')
                    )
                    ->preload(),
                Tables\Filters\SelectFilter::make('medida')
                    ->options([
                        '275/80 R22.5' => '275/80 R22.5',
                        '295/80 R22.5' => '295/80 R22.5',
                    ]),
                Tables\Filters\SelectFilter::make('descricao')
                    ->label('Desenho Recap')
                    ->relationship('ultimoRecap.desenhoPneu', 'descricao', function (Builder $query) {
                        $query->where('estado_pneu', 'RECAPADO');
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('conserto')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->form(fn(Forms\Form $form) => $form
                            ->columns(4)
                            ->schema([
                                ConsertoResource::getDataConsertoFormField(),
                                ConsertoResource::getTipoConsertoFormField(),
                                ConsertoResource::getValorConsertoFormField(),
                                ConsertoResource::getGarantiaFormField(),
                            ]))
                        ->action(fn(Pneu $record, array $data) => (new ConsertoService())->create($record, $data)),
                    Tables\Actions\Action::make('recapagem')
                        ->icon('heroicon-o-wrench')
                        ->form(fn(Forms\Form $form) => $form
                            ->columns(4)
                            ->schema([
                                ConsertoResource::getDataConsertoFormField(),
                                ConsertoResource::getTipoConsertoFormField(),
                                ConsertoResource::getValorConsertoFormField(),
                                ConsertoResource::getGarantiaFormField(),
                            ]))
                        ->action(fn(Pneu $record, array $data) => $record->consertar($record, $data)),

                ]),
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
                        'ciclo_vida'        => $record->ciclo,
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
