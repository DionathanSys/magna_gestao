<?php

namespace App\Services\OrdemServico;

use App\Models\OrdemServico;

class OrdemServicoService
{
    public function create(array $data): ?OrdemServico
    {
        return OrdemServico::create($data);
    }
}
