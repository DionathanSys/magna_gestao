<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\ItemOrdemServicoResource\Pages;
use App\Filament\Resources\ItemOrdemServicoResource\RelationManagers;
use App\Models\ItemOrdemServico;
use App\Models\Servico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemOrdemServicoResource extends Resource
{
    protected static ?string $model = ItemOrdemServico::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Itens OS';

    protected static ?string $pluralLabel = 'Itens OS';

    protected static ?string $label = 'Item OS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço'),
                Tables\Columns\TextColumn::make('posicao')
                    ->label('Posição')
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação')
                    ->placeholder('N/A'),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options(StatusOrdemServicoEnum::toSelectArray()),
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
                Tables\Columns\TextColumn::make('created_by.name')
                    ->label('Criado por')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItemOrdemServicos::route('/'),
            'create' => Pages\CreateItemOrdemServico::route('/create'),
            'edit' => Pages\EditItemOrdemServico::route('/{record}/edit'),
        ];
    }

    public static function getServicoIdFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('servico_id')
            ->label('Serviço')
            ->required()
            ->relationship('servico', 'descricao')
            ->searchable()
            ->preload()
            ->live()
            ->afterStateUpdated(function (Forms\Set $set, $state) {
                if($state) {
                    $servico = Servico::find($state);
                    $set('controla_posicao', $servico->controla_posicao ? 'Sim' : 'Não');
                }
            })
            ->createOptionForm(fn(Forms\Form $form) => ServicoResource::form($form))
            ->editOptionForm(fn(Forms\Form $form) => ServicoResource::form($form));
    }

    public static function getControlaPosicaoFormField(): Forms\Components\Toggle
    {
        return Forms\Components\Toggle::make('controla_posicao')
            ->label('Controla Posição')
            ->inline(false)
            ->disabled()
            ->live();
    }

    public static function getPosicaoFormField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('posicao')
            ->label('Posição')
            ->requiredIf('controla_posicao', true)
            ->minLength(2)
            ->maxLength(5);
    }

    public static function getObersavacaoFormField(): Forms\Components\Textarea
    {
        return Forms\Components\Textarea::make('observacao')
            ->label('Observação')
            ->maxLength(200);
    }

    public static function getStatusFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('status')
            ->label('Status')
            ->options(StatusOrdemServicoEnum::toSelectArray())
            ->default(StatusOrdemServicoEnum::PENDENTE->value)
            ->required();
    }
}
