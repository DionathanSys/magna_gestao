<?php

namespace App\Filament\Resources\OrdemServicoResource\Pages;

use App\Filament\Resources\OrdemServicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOrdemServicos extends ListRecords
{
    protected static string $resource = OrdemServicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('OS')
                ->icon('heroicon-o-plus')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = Auth::user()->id;
                    return $data;
                }),
        ];
    }
}
