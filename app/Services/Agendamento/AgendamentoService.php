<?php

namespace App\Services\Agendamento;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Models;
use App\Services\ItemOrdemServico\ItemOrdemServicoService;
use App\Services\OrdemServico\OrdemServicoService;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Auth;

class AgendamentoService
{
    use ServiceResponseTrait;

    protected OrdemServicoService $ordemServicoService;
    protected ItemOrdemServicoService $itemOrdemServicoService;

    public function __construct()
    {
        $this->ordemServicoService      = new OrdemServicoService();
        $this->itemOrdemServicoService  = new ItemOrdemServicoService();
    }

    public function create(array $data): ?Models\Agendamento
    {
        try {
            $agendamento = (new Actions\CriarAgendamento())->handle($data);
            $this->setSuccess('Agendamento criado com sucesso!');
            return $agendamento;
        } catch (\Exception $e) {
           $this->setError($e->getMessage());
           return null;
        }
    }

    public function encerrar(Models\Agendamento $agendamento)
    {
        try {
            $agendamento = (new Actions\EncerrarAgendamento($agendamento))->handle();
            $this->setSuccess('Agendamento encerrado com sucesso!');
            return $agendamento;
        } catch (\Exception $e) {
           $this->setError($e->getMessage());
           return null;
        }

    }

    // public function incluirEmOrdemServico()
    // {
    //     try {
    //         $ordemServico = $this->ordemServicoService->firstOrCreate([
    //             'veiculo_id'    => $this->agendamento->veiculo_id,
    //             'parceiro_id'   => $this->agendamento->parceiro_id,
    //         ]);

    //         $this->itemOrdemServicoService->create([
    //             'ordem_servico_id'      => $ordemServico->id,
    //             'servico_id'            => $this->agendamento->servico_id,
    //             'plano_preventivo_id'   => $this->agendamento->plano_preventivo_id,
    //             'posicao'               => $this->agendamento->posicao,
    //             'observacao'            => $this->agendamento->observacao,
    //             'status'                => StatusOrdemServicoEnum::PENDENTE,
    //             'created_by'            => Auth::user()->id,
    //         ]);

    //         $this->update([
    //             'ordem_servico_id' => $ordemServico->id,
    //         ]);

    //         //TODO: Implementar verificação de plano preventivo

    //         return $this->setSuccess('Agendamento incluído em Ordem de Serviço com sucesso.');
    //     } catch (\Exception $e) {
    //         return $this->setError('Erro ao incluir agendamento em ordem de serviço: ' . $e->getMessage());
    //     }
    // }

    // public function cancelar(): self
    // {
    //     try {
    //         $this->update([
    //             'data_agendamento' => null,
    //             'status'        => StatusOrdemServicoEnum::CANCELADO,
    //             'updated_by'    => Auth::user()->id,
    //         ]);

    //         return $this->setSuccess('Agendamento cancelado com sucesso.');
    //     } catch (\Exception $e) {
    //         return $this->setError('Erro ao cancelar agendamento: ' . $e->getMessage());
    //     }
    // }



    // public function update(array $data): void
    // {
    //     $this->agendamento
    //         ->update($data);
    // }
}
