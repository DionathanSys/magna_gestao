<?php

namespace App\Models;

use App\Enum\Pneu\LocalPneuEnum;
use App\Enum\Pneu\StatusPneuEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};

class Pneu extends Model
{
    protected $casts = [
        'local' => LocalPneuEnum::class,
        'status' => StatusPneuEnum::class,
    ];

    public function desenhoPneu(): BelongsTo
    {
        return $this->belongsTo(DesenhoPneu::class, 'desenho_pneu_id');
    }

    public function consertos(): HasMany
    {
        return $this->hasMany(Conserto::class, 'pneu_id');
    }

    public function veiculo(): HasMany
    {
        return $this->hasMany(PneuPosicaoVeiculo::class);
    }

    public function ultimoRecap()
    {
        return $this->hasOne(Recapagem::class, 'pneu_id')->latestOfMany();
    }
}
