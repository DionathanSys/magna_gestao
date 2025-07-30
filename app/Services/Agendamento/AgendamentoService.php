<?php

namespace App\Services\Agendamento;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Models;
use App\Services\ItemOrdemServico\ItemOrdemServicoService;
use App\Services\OrdemServico\OrdemServicoService;
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
        
        return true;
    }

    public function cancelar(int $agendamentoId)
    {
        return true;
    }

    public function update(array $data)
    {
        $this->agendamento->query()
            ->update($data);
    }
}
