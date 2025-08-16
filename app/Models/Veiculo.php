<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany, HasOne};
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    /**
     * Acessor para expor somente o valor numérico da quilometragem atual.
     * Disponível como $veiculo->km_atual_valor
     * (não conflita com a relação kmAtual()).
     */
    protected function kmAtualValor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('kmAtual')
                ? ($this->kmAtual?->quilometragem ?? 0)
                : ($this->kmAtual()->value('quilometragem') ?? 0)
        );
    }

    public function planoPreventivo(): BelongsToMany
    {
        return $this->belongsToMany(PlanoPreventivo::class, 'planos_manutencao_veiculo', 'veiculo_id', 'plano_preventivo_id');
    }


}
