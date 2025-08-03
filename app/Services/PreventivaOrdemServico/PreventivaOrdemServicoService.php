<?php

namespace App\Services\PreventivaOrdemServico;

use App\Models;
use App\Services\ItemOrdemServico\ItemOrdemServicoService;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Log;

class PreventivaOrdemServicoService
{
    use ServiceResponseTrait;

    protected ItemOrdemServicoService $itemOrdemServicoService;

    public function __construct()
    {
        $this->itemOrdemServicoService = new ItemOrdemServicoService();
    }

    public function create(array $data): ?Models\PlanoManutencaoOrdemServico
    {
        try {

            Log::debug(__METHOD__, ['data' => $data]);

            $preventivaOrdemServico = (new Actions\CriarVinculo())->handle($data);

            $itensPlano = $preventivaOrdemServico->planoPreventivo->itens;

            foreach ($itensPlano as $item) {
                $this->itemOrdemServicoService->create([
                    'plano_preventivo_id'   => $preventivaOrdemServico->plano_preventivo_id,
                    'ordem_servico_id'      => $preventivaOrdemServico->ordem_servico_id,
                    'servico_id'            => $item->servico_id,
                ]);
            }

            $this->setSuccess('Plano Preventivo vinculado à Ordem de Serviço com sucesso!');
            return $preventivaOrdemServico;
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            $this->setError($e->getMessage());
            return null;
        }
    }
}
