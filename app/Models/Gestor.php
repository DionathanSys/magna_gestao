<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gestor extends Model
{
    protected $table = 'gestores';

    public function indicadores(): BelongsToMany
    {
        return $this->belongsToMany(Indicador::class, 'gestor_indicador', 'gestor_id', 'indicador_id');
    }
}

