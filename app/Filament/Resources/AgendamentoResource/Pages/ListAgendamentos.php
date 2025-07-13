<?php

namespace App\Filament\Resources\AgendamentoResource\Pages;

use App\Filament\Resources\AgendamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListAgendamentos extends ListRecords
{
    protected static string $resource = AgendamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::user()->id;
                        $data['updated_by'] = Auth::user()->id;
                        return $data;
                    }),
        ];
    }
}
