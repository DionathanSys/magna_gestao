<?php

namespace App\Filament\Indicadores\Resources\ResultadoResource\Pages;

use App\Filament\Indicadores\Resources\ResultadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use App\Models;

class ListResultados extends ListRecords
{
    protected static string $resource = ResultadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('add-resultado-coletivo')
                ->form(function (\Filament\Forms\Form $form){
                    return $form
                        ->schema([
                            Forms\Components\Select::make('indicador_id')
                                ->label('Indicador')
                                ->options(Models\Indicador::all()
                                    ->where('tipo', 'COLETIVO')
                                    ->pluck('descricao', 'id'))
                                ->required(),
                            ResultadoResource::getObjetivoFormField(),
                            ResultadoResource::getResultadoFormField(),
                        ]);
                })
                ->action(function (Actions\Action $action, array $data) {
                    // Implement the logic to handle the action
                    // For example, you might want to create a new ResultadoColetivo
                    // and redirect to its edit page.
                })
        ];
    }
}
