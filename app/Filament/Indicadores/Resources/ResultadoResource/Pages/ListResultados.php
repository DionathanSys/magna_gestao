<?php

namespace App\Filament\Indicadores\Resources\ResultadoResource\Pages;

use App\Filament\Indicadores\Resources\ResultadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use App\Models;
use Filament\Support\Enums\MaxWidth;
use App\Services\NotificacaoService as notify;

class ListResultados extends ListRecords
{
    protected static string $resource = ResultadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Resultado Individual')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('add-resultado-coletivo')
                ->label('Resultado Coletivo')
                ->icon('heroicon-o-plus')
                ->modalWidth(MaxWidth::Medium)
                ->form(function (\Filament\Forms\Form $form){
                    return $form
                        ->columns(4)
                        ->schema([
                            Forms\Components\Select::make('indicador_id')
                                ->label('Indicador')
                                ->columnSpanFull()
                                ->native(false)
                                ->options(Models\Indicador::all()
                                    ->where('tipo', 'COLETIVO')
                                    ->pluck('descricao', 'id'))
                                ->required(),
                            ResultadoResource::getObjetivoFormField()
                                ->columnSpan(1),
                            ResultadoResource::getResultadoFormField()
                                ->columnSpan(1),
                            ResultadoResource::getPeriodoFormField()
                                ->columnSpan(2),
                        ]);
                })
                ->action(function (Actions\Action $action, array $data) {
                    $service = new \App\Services\Indicador\IndicadorService();
                    $resultado = $service->createResultadoColetivo($data);

                    if ($service->hasError()) {
                        notify::error(mensagem: $service->getMessage());
                        $action->halt();
                    }

                    notify::success(mensagem: 'Resultado coletivo criado com sucesso!');
                    return $resultado;
                })
        ];
    }
}
