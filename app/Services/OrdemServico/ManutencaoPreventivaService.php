<?php

namespace App\Services\OrdemServico;

use App\Models\OrdemServico;
use App\Models\PlanoManutencaoOrdemServico;
use App\Services\NotificacaoService as notify;

class ManutencaoPreventivaService
{
    public static function associarPlanoPreventivo(OrdemServico $ordemServico, $planoPreventivoId)
    {
        $manutencaoPreventivaAssociada = PlanoManutencaoOrdemServico::query()
            ->where('ordem_servico_id', $ordemServico->id)
            ->where('plano_preventivo_id', $planoPreventivoId)
            ->first();

        if ($manutencaoPreventivaAssociada) {
            notify::error('Plano Preventivo já associado a esta Ordem de Serviço.');
            return;
        }

        $manutencaoPreventivaAssociada = PlanoManutencaoOrdemServico::create([
            'plano_preventivo_id'   => $planoPreventivoId,
            'ordem_servico_id'      => $ordemServico->id,
            'veiculo_id'            => $ordemServico->veiculo_id,
            'km_execucao'           => $ordemServico->quilometragem,
            'data_execucao'         => $ordemServico->data_fim,
        ]);

        dd($manutencaoPreventivaAssociada);


        if ($manutencaoPreventivaAssociada) {
            notify::success('Plano Preventivo associado com sucesso.');
        } else {
            notify::error('Erro ao associar Plano Preventivo.');
        }
    }
}
