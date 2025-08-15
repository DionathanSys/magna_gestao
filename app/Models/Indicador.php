<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Indicador extends Model
{
    protected $table = 'indicadores';

    public function gestores(): BelongsToMany
    {
        return $this->belongsToMany(Gestor::class, 'gestor_indicador', 'indicador_id', 'gestor_id');
    }
}
