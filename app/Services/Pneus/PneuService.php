<?php

namespace App\Services\Pneus;

use App\Models\Pneu;
use App\Models\Recapagem;

class PneuService
{
    public static function atualizarCicloVida(Recapagem $recapagem)
    {
        $pneu = Pneu::find($recapagem->pneu_id);
        $pneu->ciclo_vida = $pneu->ciclo_vida + 1;
        $pneu->save();
    }
}
