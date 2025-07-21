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

    public function viagem()
    {
        return $this->belongsTo(Viagem::class);
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function integrado()
    {
        return $this->belongsTo(Integrado::class, 'integrado_id');
    }

}
