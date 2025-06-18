<?php

namespace App\Filament\Resources\ViagemResource\Pages;

use App\Filament\Resources\ViagemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class EditViagem extends EditRecord
{
    protected static string $resource = ViagemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Log::debug("Viagem atualizada ID: {$this->record->id}", [
            'viagem' => $this->record,
        ]);
        (new \App\Services\ViagemService())->recalcularViagem($this->record);
    }

    #[On('atualizarCadastroIntegrado')]
    public function atualizarCadastroIntegrado(array $data): void
    {
        Actions\Action::make('atualizar_cadastro_integrado')
            ->action(function () use ($data) {
                dd($data);
            })
            ->requiresConfirmation()
            ->icon('heroicon-o-refresh')
            ->label('Atualizar Cadastro Integrado');
    }
}
