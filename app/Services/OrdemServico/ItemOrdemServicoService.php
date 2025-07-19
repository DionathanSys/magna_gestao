<?php

namespace App\Services\OrdemServico;

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

}
