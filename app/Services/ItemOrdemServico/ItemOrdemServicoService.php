<?php

namespace App\Services\ItemOrdemServico;

use App\Models\ItemOrdemServico;

class ItemOrdemServicoService
{
    public function __construct()
    {
        // Constructor logic if needed
    }

    public function create(array $data)
    {
        // Logic to create an item in the service order
        ItemOrdemServico::query()->updateOrCreate(
            [
                'ordem_servico_id' => $data['ordem_servico_id'],
                'servico_id'       => $data['servico_id'],
            ],
            [
                'plano_preventivo_id' => $data['plano_preventivo_id'] ?? null,
                'posicao'             => $data['posicao'] ?? null,
                'observacao'          => $data['observacao'] ?? null,
                'status'              => $data['status'] ?? null,
                'created_by'          => $data['created_by'] ?? null,
                
            ]
        );
    }
}
