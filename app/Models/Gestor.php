<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gestor extends Model
{
    protected $table = 'gestores';

    public function indicadores(): BelongsToMany
    {
        return $this->belongsToMany(Indicador::class, 'gestor_indicador', 'gestor_id', 'indicador_id');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class, 'gestor_id');
    }
}

