<?php

namespace App\Filament\Resources\PneuResource\Pages;

use App\Enum\Pneu\LocalPneuEnum;
use App\Filament\Resources\PneuResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListPneus extends ListRecords
{
    protected static string $resource = PneuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Pneu')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Estoque' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('local', LocalPneuEnum::ESTOQUE_CCO)),
            'Frota' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('local', LocalPneuEnum::FROTA)),
            'Outros' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotIn('local', [LocalPneuEnum::ESTOQUE_CCO, LocalPneuEnum::FROTA])),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Estoque';
    }

    // public function mount(): void
    // {
    //     parent::mount();
    //     $this->activeTab = session('listOrdensServicoTab', $this->getDefaultActiveTab());
    // }

    // public function updatedActiveTab(): void
    // {
    //     parent::updatedActiveTab();
    //     session(['listOrdensServicoTab' => $this->activeTab]);
    // }
}
