<?php

namespace App\Filament\Resources;

use App\Enum\Pneu\LocalPneuEnum;
use App\Enum\Pneu\StatusPneuEnum;
use App\Filament\Resources\RecapagemResource\Pages;
use App\Filament\Resources\RecapagemResource\RelationManagers;
use App\Models\Pneu;
use App\Models\Recapagem;
use App\Services\Pneus\PneuService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecapagemResource extends Resource
{
    protected static ?string $model = Recapagem::class;

    protected static ?string $navigationGroup = 'Pneus';

    protected static ?string $pluralModelLabel = 'Recapagens';

    protected static ?string $pluralLabel = 'Recapagens';

    protected static ?string $label = 'Recapagem';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Forms\Components\Select::make('pneu_id')
                    ->label('Pneu')
                    ->relationship('pneu', 'numero_fogo', function (Builder $query) {
                        $query->where('status', StatusPneuEnum::DISPONIVEL)
                            ->where('local', LocalPneuEnum::ESTOQUE_CCO);
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('data_recapagem')
                    ->date('d/m/Y')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->maxDate(now())
                    ->required(),
                Forms\Components\Select::make('desenho_pneu_id')
                    ->label('Desenho do Pneu')
                    ->relationship('desenhoPneu', 'descricao')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('parceiro_id')
                    ->label('Parceiro')
                    ->relationship('parceiro', 'nome')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->label('Nº Fogo')
                    ->numeric(null, '', '')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('data_recapagem')
                    ->label('Data Recap')
                    ->date('d/m/Y')
                    ->width('1%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pneu.modelo')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('desenhoPneu.descricao')
                    ->label('Borracha')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('desenhoPneu.modelo')
                    ->label('Modelo')
                    ->width('1%')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('parceiro.nome')
                    ->numeric()
                    ->width('1%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->width('1%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchOnBlur(true)
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\ReplicateAction::make()
                    ->icon('heroicon-o-document-duplicate')
                    ->iconButton()
                    ->fillForm(fn(Recapagem $record) => [
                        'data_recapagem'  => $record->data_recapagem,
                        'desenho_pneu_id' => $record->desenho_pneu_id,
                        'parceiro_id'     => $record->parceiro_id,
                    ])
                    ->form(fn(Forms\Form $form) => static::form($form)->columns(4))
                    ->excludeAttributes([
                        'id',
                        'created_at',
                        'updated_at',
                    ])
                    ->after(fn(Recapagem $record) => PneuService::atualizarCicloVida($record)),
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
            'index' => Pages\ListRecapagems::route('/'),
            // 'create' => Pages\CreateRecapagem::route('/create'),
            // 'edit' => Pages\EditRecapagem::route('/{record}/edit'),
        ];
    }
}
