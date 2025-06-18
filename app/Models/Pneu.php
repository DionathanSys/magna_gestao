<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pneu extends Model
{
    public function desenhoPneu(): BelongsTo
    {
        return $this->belongsTo(DesenhoPneu::class, 'desenho_pneu_id');
    }
}
