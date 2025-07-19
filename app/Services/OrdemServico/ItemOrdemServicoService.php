<?php

namespace App\Services\OrdemServico;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Enum\OrdemServico\TipoManutencaoEnum;
use App\Models\ItemOrdemServico;
use App\Models\OrdemServico;
use App\Services\NotificacaoService as notify;

class ItemOrdemServicoService
{
    public static function create(array $data)
    {
        ItemOrdemServico::query()->updateOrCreate(
            [
                'ordem_servico_id'  => $data['ordem_servico_id'],
                'servico_id'        => $data['servico_id'],
            ],
            [
                'ordem_servico_id'  => $data['ordem_servico_id'],
                'servico_id'        => $data['servico_id'],
                'posicao'           => $data['posicao'] ?? null,
                'observacao'        => $data['observacao'] ?? null,
                'status'            => $data['status'] ?? null,
                'created_by'        => $data['created_by'] ?? null,
            ]
        );
    }

    public static function delete(ItemOrdemServico $itemOrdemServico)
    {
        if ($itemOrdemServico->status != StatusOrdemServicoEnum::PENDENTE) {
            notify::error('Não é possível remover um item de ordem de serviço que não esteja pendente.');
            return;
        }

        if($itemOrdemServico->servico->tipo == TipoManutencaoEnum::PREVENTIVA) {
            notify::alert('Removido um item de manutenção preventiva, verifique se a ordem de serviço não está vinculada a um plano preventivo.');
        }


        $itemOrdemServico->delete();

        notify::success('Item de Ordem de Serviço removido com sucesso.');
    }

}
