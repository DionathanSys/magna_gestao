<?php

namespace App\Services\OrdemServico;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Models\Agendamento;
use App\Models\OrdemSankhya;
use App\Services\NotificacaoService as notify;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AgendamentoService
{

    protected OrdemServicoService $ordemServicoService;

    public function __construct()
    {
        $this->ordemServicoService = new OrdemServicoService();
    }

    public function gerarOrdemServico(Agendamento $agendamento): void
    {
        if($agendamento->ordemServico) {
            notify::error('Agendamento já possui uma Ordem de Serviço associada.');
            return;
        }

        $ordemServico = $this->ordemServicoService->create([
            'veiculo_id'    => $agendamento->veiculo_id,
            'quilometragem' => null,
            'data_inicio'   => now(),
            'status'        => StatusOrdemServicoEnum::PENDENTE,
            'status_sankhya' => StatusOrdemServicoEnum::PENDENTE,
            'observacao'    => $agendamento->observacao,
            'servico_id'    => $agendamento->servico_id,
            'parceiro_id'   => $agendamento->parceiro_id ?? null,
            'created_by'    => Auth::user()->id,
        ]);

        if ($ordemServico) {
            $ordemServico->itens()->create([
                'servico_id' => $agendamento->servico_id,
                'posicao'    => $agendamento->servico->controla_posicao ? 1 : null,
                'observacao' => $agendamento->observacao,
                'status'     => StatusOrdemServicoEnum::PENDENTE,
            ]);
        }
    }
}
