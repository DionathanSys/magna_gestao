<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PneuPosicaoVeiculo extends Model
{
    protected $table = 'pneu_posicao_veiculo';

    public function pneu()
    {
        return $this->belongsTo(Pneu::class, 'pneu_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

}
