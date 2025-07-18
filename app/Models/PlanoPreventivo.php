<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoPreventivo extends Model
{
    protected $table = 'planos_preventivo';

    protected $casts = [
        'itens' => 'array',
    ];
}
