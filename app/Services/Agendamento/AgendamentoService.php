<?php

namespace App\Services\Agendamento;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Models;
use App\Services\ItemOrdemServico\ItemOrdemServicoService;
use App\Services\OrdemServico\OrdemServicoService;
use App\Services\NotificacaoService as notify;
use Illuminate\Support\Facades\Auth;

class AgendamentoService
{
    protected OrdemServicoService $ordemServicoService;
    protected ItemOrdemServicoService $itemOrdemServicoService;

    public function __construct(protected Models\Agendamento $agendamento)
    {
        $this->ordemServicoService = new OrdemServicoService();
        $this->itemOrdemServicoService = new ItemOrdemServicoService();
    }

    public function incluirEmOrdemServico()
    {
        $ordemServico = $this->ordemServicoService->firstOrCreate([
            'veiculo_id'    => $this->agendamento->veiculo_id,
            'parceiro_id'   => $this->agendamento->parceiro_id,
        ]);

        $this->itemOrdemServicoService->create([
            'ordem_servico_id'      => $ordemServico->id,
            'servico_id'            => $this->agendamento->servico_id,
            'plano_preventivo_id'   => $this->agendamento->plano_preventivo_id,
            'posicao'               => $this->agendamento->posicao,
            'observacao'            => $this->agendamento->observacao,
            'status'                => StatusOrdemServicoEnum::PENDENTE,
            'created_by'            => Auth::user()->id,
        ]);

        $this->update([
            'ordem_servico_id' => $ordemServico->id,
        ]);

        //TODO: Implementar verificação de plano preventivo

        notify::success('Agendamento incluído em Ordem de Serviço com sucesso.');

        return true;
    }

    public function cancelar(): void
    {
        $this->update([
            'data_agendamento' => null,
            'status'        => StatusOrdemServicoEnum::CANCELADO,
            'updated_by'    => Auth::user()->id,
        ]);

        notify::success('Agendamento cancelado com sucesso.');
    }

    public function encerrar(): void
    {
        $this->update([
            'data_realizado' => now(),
            'status'        => StatusOrdemServicoEnum::CONCLUIDO,
            'updated_by'    => Auth::user()->id,
        ]);

        notify::success('Agendamento encerrado com sucesso.');
    }

    public function update(array $data): void
    {
        $this->agendamento
            ->update($data);
    }
}
