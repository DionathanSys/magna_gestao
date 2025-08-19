<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enum\PeriodicidadeEnum;

class Indicador extends Model
{
    protected $table = 'indicadores';

    public function gestores(): BelongsToMany
    {
        return $this->belongsToMany(Gestor::class, 'gestor_indicador', 'indicador_id', 'gestor_id');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class, 'indicador_id');
    }

    public function pesoPorPeriodo(): Attribute
    {
        return Attribute::make(
            get:  fn () => round($this->peso / PeriodicidadeEnum::from($this->periodicidade)->periodicidadeAno(), 4)
        );
    }
}
