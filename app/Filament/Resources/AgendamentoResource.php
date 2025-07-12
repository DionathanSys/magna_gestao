<?php

namespace App\Filament\Resources;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Filament\Resources\AgendamentoResource\Pages;
use App\Filament\Resources\AgendamentoResource\RelationManagers;
use App\Models\Agendamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationGroup = 'Mant.';

    protected static ?string $pluralModelLabel = 'Agendamentos';

    protected static ?string $pluralLabel = 'Agendamentos';

    protected static ?string $label = 'Agendamento';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                Forms\Components\TextInput::make('ordem_servico_id')
                    ->label('Ordem de Serviço')
                    ->visible(fn () => Auth::user()->is_admin)
                    ->exists(
                        Agendamento::class,
                        'id',
                        fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class])
                    )
                    ->numeric(),
                OrdemServicoResource::getVeiculoIdFormField(),
                Forms\Components\DatePicker::make('data_agendamento'),
                ItemOrdemServicoResource::getServicoIdFormField(),
                Forms\Components\Select::make('status')
                    ->options(StatusOrdemServicoEnum::toSelectArray()),
                Forms\Components\Textarea::make('observacao')
                    ->label('Observação')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordem_servico_id')
                    ->label('OS ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_agendamento')
                    ->label('Agendado Para')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('servico.descricao')
                    ->label('Serviço')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('observacao')
                    ->label('Observação'),
                Tables\Columns\TextColumn::make('created_by')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListAgendamentos::route('/'),
            'edit' => Pages\EditAgendamento::route('/{record}/edit'),
        ];
    }
}
