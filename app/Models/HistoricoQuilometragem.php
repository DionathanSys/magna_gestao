<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoQuilometragem extends Model
{
    protected $table = 'historico_quilometragens';

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }
}
