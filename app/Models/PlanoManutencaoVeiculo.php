<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoManutencaoVeiculo extends Model
{
    protected $table = 'planos_manutencao_veiculo';

    public function planoPreventivo()
    {
        return $this->belongsTo(PlanoPreventivo::class, 'plano_preventivo_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }
}
