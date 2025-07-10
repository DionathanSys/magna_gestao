<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class HistoricoMovimentoPneu extends Model
{
    protected $table = 'historico_movimento_pneus';

    public function pneu()
    {
        return $this->belongsTo(Pneu::class);
    }
    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function kmPercorrido(): Attribute
    {
        return Attribute::get(
            fn () => ($this->km_final ?? 0) - ($this->km_inicial ?? 0)
        );
    }
}
