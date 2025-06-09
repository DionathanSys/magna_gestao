<?php

namespace App\Services;

use App\DTO\DocumentoFreteDTO;
use App\Models\DocumentoFrete;

class DocumentoFreteService
{

    public DocumentoFrete $documentoFrete;

    public function __construct()
    {
        $this->documentoFrete = new DocumentoFrete();
    }
    public function create(DocumentoFreteDTO $documentoFreteDto)
    {
        return $this->documentoFrete->create($documentoFreteDto->toArray());
    }

}
