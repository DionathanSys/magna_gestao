<?php

namespace App\Models;

use App\Enum\Viagem\StatusViagemEnum;
use Illuminate\Database\Eloquent\Model;

class ViagemComplemento extends Model
{
    protected $casts = [
        'conferido' => 'boolean',
        'status' => StatusViagemEnum::class,
    ];
}
