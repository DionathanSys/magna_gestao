<?php

// Exemplo de uso no ListAgendamento (Livewire Component)

namespace App\Livewire;

use App\Models\Agendamento;
use App\Services\Agendamento\AgendamentoService;
use Livewire\Component;

class ListAgendamento extends Component
{
    public function cancelarAgendamento($agendamentoId)
    {
        $agendamento = Agendamento::find($agendamentoId);
        
        if (!$agendamento) {
            session()->flash('error', 'Agendamento não encontrado.');
            return;
        }

        $service = new AgendamentoService($agendamento);
        $service->cancelar();

        // Usando a trait para exibir mensagens
        if ($service->hasError()) {
            session()->flash('error', $service->getMessage());
        } else {
            session()->flash('success', $service->getMessage());
        }

        // Se precisar dos dados adicionais
        $data = $service->getData();
        
        // Refresh da lista
        $this->render();
    }

    public function incluirEmOrdemServico($agendamentoId)
    {
        $agendamento = Agendamento::find($agendamentoId);
        
        if (!$agendamento) {
            session()->flash('error', 'Agendamento não encontrado.');
            return;
        }

        $service = new AgendamentoService($agendamento);
        $service->incluirEmOrdemServico();

        // Verificar se houve erro
        if ($service->hasError()) {
            session()->flash('error', $service->getMessage());
        } else {
            session()->flash('success', $service->getMessage());
        }

        // Ou usar a resposta completa
        $response = $service->getResponse();
        /*
        $response = [
            'success' => true/false,
            'message' => 'Mensagem',
            'type' => 'success/error/warning/info',
            'data' => []
        ];
        */

        $this->render();
    }

    public function render()
    {
        return view('livewire.list-agendamento');
    }
}
