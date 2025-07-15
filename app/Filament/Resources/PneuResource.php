<?php

namespace App\Filament\Resources;

use App\Enum\Pneu\LocalPneuEnum;
use App\Enum\Pneu\MotivoMovimentoPneuEnum;
use App\Enum\Pneu\StatusPneuEnum;
use App\Filament\Resources\PneuResource\Pages;
use App\Filament\Resources\PneuResource\RelationManagers;
use App\Models\Pneu;
use App\Models\Recapagem;
use App\Services\Pneus\ConsertoService;
use App\Services\Pneus\PneuService;
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
                Forms\Components\Select::make('marca')
                    ->options([
                        'MICHELIN' => 'MICHELIN',
                        'X BRI' => 'X BRI',
                        'GOODYEAR' => 'GOODYEAR',
                        'PIRELLI' => 'PIRELLI',
                        'SPEEDMAX PRIME' => 'SPEEDMAX PRIME',
                        'DUNLOP' => 'DUNLOP',
                        'STRONG TRAC' => 'STRONG TRAC',
                        'CONTINENTAL' => 'CONTINENTAL',
                    ]),
                Forms\Components\Select::make('modelo')
                    ->options([
                        'X WORKS' => 'X WORKS',
                        'X WORKS Z' => 'X WORKS Z',
                        'FORZA BLOCK' => 'FORZA BLOCK',
                        'DPLUS' => 'DPLUS',
                        'X MULTI Z' => 'X MULTI Z',
                        'X MULTI D' => 'X MULTI D',
                        'MIXMAX A' => 'MIXMAX A',
                        'MIX WORKS' => 'MIX WORKS',
                        'SP320' => 'SP320',
                        'HSR2' => 'HSR2',
                        'FG-01' => 'FG-01',
                        'TG-01' => 'TG-01',
                        'FR-88' => 'FR-88',
                        'G686 MSS PLUS' => 'G686 MSS PLUS',
                        'CHD3' => 'CHD3',



                    ]),
                Forms\Components\Select::make('medida')
                    ->options([
                        '275/80 R22.5' => '275/80 R22.5',
                        '295/80 R22.5' => '295/80 R22.5',
                    ])
                    ->default('275/80 R22.5'),
                Forms\Components\TextInput::make('ciclo_vida')
                    ->label('Vida')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(3),
                Forms\Components\TextInput::make('valor')
                    ->label('Valor')
                    ->numeric()
                    ->prefix('R$'),
                Forms\Components\Select::make('desenho_pneu_id')
                    ->label('Desenho Borracha')
                    ->relationship('desenhoPneu', 'descricao')
                    ->searchable()
                    ->required()
                    ->createOptionForm(fn(Form $form) => DesenhoPneuResource::form($form)),
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
                Tables\Columns\TextColumn::make('veiculo.veiculo.placa')
                    ->label('Placa')
                    ->width('1%')
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->url(fn (Pneu $record): string => VeiculoResource::getUrl('edit', ['record' => $record->veiculo->veiculo->id ?? 0]))
                    ->openUrlInNewTab(),
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
                                Forms\Components\DatePicker::make('data_recapagem')
                                    ->date('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->required(),
                                Forms\Components\Select::make('desenho_pneu_id')
                                    ->label('Desenho do Pneu')
                                    ->columnSpan(2)
                                    ->relationship('desenhoPneu', 'descricao')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn(Form $form) => DesenhoPneuResource::form($form)),
                                Forms\Components\TextInput::make('valor')
                                    ->label('Valor')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('R$'),
                            ]))
                        ->action(function (Pneu $record, array $data) {
                            Recapagem::create([
                                'pneu_id'          => $record->id,
                                'data_recapagem'   => $data['data_recapagem'],
                                'desenho_pneu_id'  => $data['desenho_pneu_id'],
                                'valor'            => $data['valor'],
                                'parceiro_id'      => 1,
                            ]);
                        })
                        // ->after(fn(Pneu $record) => PneuService::atualizarCicloVida($record->ultimoRecap))
                        ,

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
                        'valor'             => $record->valor,
                        'ciclo_vida'        => $record->ciclo,
                    ])
                    ->form(fn(Forms\Form $form) => static::form($form)->columns(4))
                    ->excludeAttributes([
                        'id',
                        'numero_fogo',
                        'created_at',
                        'updated_at',
                    ]),
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
