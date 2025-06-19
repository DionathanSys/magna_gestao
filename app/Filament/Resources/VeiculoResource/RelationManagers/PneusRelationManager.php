<?php

namespace App\Filament\Resources\VeiculoResource\RelationManagers;

use App\Models\Pneu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PneusRelationManager extends RelationManager
{
    protected static string $relationship = 'pneus';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pneu_id')
                    ->label('Pneu')
                    ->options(
                        Pneu::query()
                            ->whereDoesntHave('veiculo', function (Builder $query) {
                                $query->where('veiculo_id', $this->ownerRecord->id);
                            })
                            ->pluck('numero_fogo', 'id')
                    )
                    ->searchable(),
                Forms\Components\TextInput::make('eixo')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('posicao')
                    ->label('Posição')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('km_inicial')
                    ->label('KM Inicial')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('data_inicial')
                    ->label('Dt. Aplicação')
                    ->date()
                    ->default(now())
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->label('Pneu')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('posicao')
                    ->label('Posição')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('eixo')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('km_inicial')
                    ->width('1%'),
                Tables\Columns\TextColumn::make('data_inicial')
                    ->width('1%')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->groups([
                Tables\Grouping\Group::make('eixo')
                    ->label('Eixo')
                    ->collapsible(),
            ])
            ->defaultGroup('eixo')
            ->defaultSort('id')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Pneu')
                    ->icon('heroicon-o-plus-circle'),
                Tables\Actions\Action::make('vincular-pneu')
                    ->label('Vincular Pneu')
                    ->icon('heroicon-o-link')
                    ->form([
                        Forms\Components\Select::make('pneu_id')
                            ->label('Pneu')
                            ->options(
                                Pneu::query()
                                    ->whereDoesntHave('veiculo', function (Builder $query) {
                                        $query->where('veiculo_id', $this->ownerRecord->id);
                                    })
                                    ->pluck('numero_fogo', 'id')
                            )
                            ->searchable()
                            ->required(),
                    ])->action(function (array $data) {
                        $this->ownerRecord->pneus()->attach($data['pneu_id']);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
