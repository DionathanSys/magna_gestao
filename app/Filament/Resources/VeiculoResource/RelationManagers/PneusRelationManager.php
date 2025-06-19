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
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('veiculo_id')
                    ->label('Veículo')
                    ->relationship('veiculo', 'placa')
                    ->required(),
                Forms\Components\TextInput::make('posicao')
                    ->label('Posição')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('eixo')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('km_inicial')
                    ->label('KM Inicial')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('data_inicial')
                    ->label('Dt. Aplicação')
                    ->date()
                    ->default(now())
                    ->native(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('numero_fogo'),
                Tables\Columns\TextColumn::make('km_inicial'),
                Tables\Columns\TextColumn::make('data_inicial')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
