<?php

namespace App\Services\Pneus;

use App\Models\Pneu;
use App\Models\Recapagem;

class PneuService
{
    public static function atualizarCicloVida(Recapagem $recapagem)
    {
        ds('Atualizando ciclo de vida do pneu após recapagem', [
            'pneu_id' => $recapagem->pneu_id,
            'ciclo_vida_atual' => $recapagem->pneu->ciclo_vida,
        ]);
        $pneu = Pneu::find($recapagem->pneu_id);
        $pneu->ciclo_vida = $pneu->ciclo_vida + 1;
        $pneu->save();
    }
}
