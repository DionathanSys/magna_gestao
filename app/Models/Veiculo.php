<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};

class Veiculo extends Model
{
    public function pneus(): HasMany
    {
        return $this->hasMany(PneuPosicaoVeiculo::class);
    }

}
