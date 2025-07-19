<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanoManutencaoVeiculo extends Model
{
    protected $table = 'planos_manutencao_veiculo';

    public function planoPreventivo(): BelongsTo
    {
        return $this->belongsTo(PlanoPreventivo::class, 'plano_preventivo_id');
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function ultimaExecucao()
    {
        return $this->hasMany(PlanoManutencaoOrdemServico::class, 'plano_preventivo_id', 'plano_preventivo_id')
            ->where('veiculo_id', $this->veiculo_id)
            ->latest();
    }
}
