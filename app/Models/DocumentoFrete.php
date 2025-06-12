<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoFrete extends Model
{
    protected $table = 'documentos_frete';

    public function viagem(): BelongsTo
    {
        return $this->belongsTo(Viagem::class, 'documento_transporte');
    }
}
