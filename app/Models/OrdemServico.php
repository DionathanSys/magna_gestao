<?php

namespace App\Models;

use App\Enum\OrdemServico\TipoManutencaoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdemServico extends Model
{
    protected $table = 'ordens_servico';

    protected $casts = [
        'tipo_manutencao' => TipoManutencaoEnum::class,
    ];

    public function parceiro(): BelongsTo
    {
        return $this->belongsTo(Parceiro::class, 'parceiro_id');
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemOrdemServico::class, 'ordem_servico_id');
    }

}
