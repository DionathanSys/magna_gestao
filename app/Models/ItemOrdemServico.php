<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemOrdemServico extends Model
{
    protected $table = 'itens_ordem_servico';

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }

}
