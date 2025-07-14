<?php

namespace App\Services\OrdemServico;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Models\Agendamento;
use App\Models\ItemOrdemServico;
use App\Models\OrdemServico;
use App\Services\NotificacaoService as notify;
use Illuminate\Support\Facades\Auth;

class OrdemServicoService
{
    protected AgendamentoService $agendamentoService;

    public function __construct()
    {
        $this->agendamentoService = new AgendamentoService();
    }

    public static function create(array $data): ?OrdemServico
    {
        return OrdemServico::create($data);
    }

    public function encerrarOrdemServico(OrdemServico $ordemServico): void
    {
        if ($ordemServico->status == StatusOrdemServicoEnum::CONCLUIDO) {
            notify::error('Ordem de Serviço já está encerrada.');
            return;
        }

        $ordemServico->update([
            'status'        => StatusOrdemServicoEnum::CONCLUIDO,
        ]);

        $ordemServico->itens()->each(function (ItemOrdemServico $item) {
            if(in_array($item->status, [StatusOrdemServicoEnum::PENDENTE, StatusOrdemServicoEnum::EXECUCAO])) {
                $item->update([
                    'status' => StatusOrdemServicoEnum::CONCLUIDO,
                ]);
            }
        });

        notify::success('Ordem de Serviço encerrada com sucesso.');
    }

    public function reagendarServico(ItemOrdemServico $item, $data = null)
    {
        if($item->status != StatusOrdemServicoEnum::PENDENTE) {
            notify::error('Serviço não pode ser reagendado, pois não está pendente.');
            return;
        }

        $item->update([
            'status' => StatusOrdemServicoEnum::ADIADO,
        ]);

        Agendamento::create([
            'ordem_servico_id'  => null,
            'veiculo_id'        => $item->ordemServico->veiculo_id,
            'data_agendamento'  => $data ?? null,
            'servico_id'        => $item->servico_id,
            'status'            => StatusOrdemServicoEnum::PENDENTE,
            'observacao'        => $item->observacao,
            'created_by'        => Auth::user()->id,
            'updated_by'        => Auth::user()->id,
            'parceiro_id'       => $item->ordemServico->parceiro_id ?? null,
        ]);
    }

    public function ordemServicoPendente(int $veiculoId): ?OrdemServico
    {
        return OrdemServico::where('veiculo_id', $veiculoId)
            ->where('status', StatusOrdemServicoEnum::PENDENTE)
            ->first();
    }
}
