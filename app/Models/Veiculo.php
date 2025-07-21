<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany, HasOne};

class Veiculo extends Model
{

    // protected $appends = ['km_atual'];
    
    public function pneus(): HasMany
    {
        return $this->hasMany(PneuPosicaoVeiculo::class);
    }

    public function kmAtual(): HasOne
    {
        return $this->hasOne(HistoricoQuilometragem::class)->latestOfMany();
    }

    public function planoPreventivo(): BelongsToMany
    {
        return $this->belongsToMany(PlanoPreventivo::class, 'planos_manutencao_veiculo', 'veiculo_id', 'plano_preventivo_id');
    }


}