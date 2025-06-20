<?php

namespace App\Filament\Resources\VeiculoResource\RelationManagers;

use App\Enum\Pneu\MotivoMovimentoPneuEnum;
use App\Filament\Resources\PneuResource;
use App\Models\Pneu;
use App\Models\PneuPosicaoVeiculo;
use App\Services\Pneus\MovimentarPneuService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PneusRelationManager extends RelationManager
{
    protected static string $relationship = 'pneus';

    protected MovimentarPneuService $movimentarPneuService;

    public function __construct()
    {
        $this->movimentarPneuService = new MovimentarPneuService();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('pneu.numero_fogo')
                    ->label('Pneu')
                    ->placeholder('Vazio')
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
            ->groupingSettingsHidden()
            ->defaultSort('id')
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Pneu')
                    ->icon('heroicon-o-plus-circle')
                    ->visible(fn() => Auth::user()->is_admin),
            ])
            ->actions([
                Tables\Actions\Action::make('desvincular-pneu')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('danger')
                    ->iconButton()
                    ->tooltip('Desvincular Pneu')
                    ->visible(fn($record) => ! $record->pneu_id == null)
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->form(fn(Forms\Form $form) => $form
                        ->columns(4)
                        ->schema([
                            PneuResource::getMotivoMovimentacaoFormField()
                                ->columnSpan(3),
                            PneuResource::getSulcoFormField()
                                ->columnSpan(1),
                            PneuResource::getDataFinalOrdemFormField()
                                ->label('Dt. Movimentação')
                                ->columnSpan(2),
                            PneuResource::getKmFinalOrdemFormField()
                                ->label('KM Movimentação')
                                ->columnSpan(2),
                            PneuResource::getObservacaoFormField(),
                        ]))
                    ->action(fn($record, array $data) => $this->movimentarPneuService->removerPneu($record, $data)),
                Tables\Actions\Action::make('vincular-pneu')
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->color('info')
                    ->iconButton()
                    ->tooltip('Vincular Pneu')
                    ->visible(fn($record) => $record->pneu_id == null)
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->form(fn(Forms\Form $form) => $form
                        ->columns(4)
                        ->schema([
                            PneuResource::getPneuDisponivelFormField()
                                ->label('Pneu Disponível')
                                ->columnSpan(3),
                            PneuResource::getDataInicialOrdemFormField()
                                ->label('Dt. Movimentação')
                                ->columnStart(1)
                                ->columnSpan(2),
                            PneuResource::getKmInicialOrdemFormField()
                                ->label('KM Movimentação')
                                ->columnSpan(2),
                        ]))
                    ->action(fn($record, array $data) => $this->movimentarPneuService->aplicarPneu($record, $data)),
                Tables\Actions\Action::make('trocar-pneu')
                    ->icon('heroicon-o-arrows-right-left')
                    ->iconButton()
                    ->tooltip('Substituir Pneu')
                    ->visible(fn($record) => ! $record->pneu_id == null)
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->form([
                        PneuResource::getMotivoMovimentacaoFormField()
                            ->label('Motivo Movimentação'),
                        PneuResource::getPneuDisponivelFormField(),
                        PneuResource::getDataInicialOrdemFormField()
                            ->label('Dt. Movimentação'),
                        PneuResource::getKmInicialOrdemFormField()
                            ->label('KM Movimentação'),
                        PneuResource::getObservacaoFormField(),
                    ])->action(fn (array $data, PneuPosicaoVeiculo $record) => $this->movimentarPneuService->trocarPneu($record, $data)),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn() => Auth::user()->is_admin),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn() => Auth::user()->is_admin),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('rodizio')
                        ->label('Rodízio')
                        ->icon('heroicon-o-arrows-right-left')
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::ExtraLarge)
                        ->form(fn(Forms\Form $form) => $form
                            ->columns(4)
                            ->schema([
                                PneuResource::getMotivoMovimentacaoFormField()
                                    ->label('Motivo Movimentação')
                                    ->columnSpan(3)
                                    ->disabled()
                                    ->default(MotivoMovimentoPneuEnum::RODIZIO->value),
                                PneuResource::getSulcoFormField()
                                    ->columnSpan(1),
                                PneuResource::getDataInicialOrdemFormField()
                                    ->label('Dt. Movimentação')
                                    ->columnSpan(2),
                                PneuResource::getKmInicialOrdemFormField()
                                    ->label('KM Movimentação')
                                    ->columnSpan(2),
                                PneuResource::getObservacaoFormField(),
                        ]))
                        ->action(fn (array $data, Collection $records) => $this->movimentarPneuService->rodizioPneu($records, $data)),

                ]),
            ]);
    }
}
