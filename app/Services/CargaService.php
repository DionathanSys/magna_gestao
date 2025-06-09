<?php

namespace App\Services;

use App\Models\CargaViagem;
use App\Models\Integrado;
use App\Models\Viagem;
use Illuminate\Support\Facades\Log;

class CargaService
{
    public CargaViagem $cargaViagem;

    public function __construct()
    {
        $this->cargaViagem = new CargaViagem();
    }

    public function create(?Integrado $integrado, Viagem $viagem): ?CargaViagem
    {

        try {

            return $this->cargaViagem->query()->updateOrCreate(
                [
                    'viagem_id'    => $viagem->id,
                    'integrado_id' => $integrado->id ?? 0,
                ],
                [
                    'viagem_id'     => $viagem->id,
                    'integrado_id'  => $integrado->id ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erro ao criar carga de viagem: ' . $e->getMessage(), [
                'viagem_id'    => $viagem->id,
                'integrado_id' => $integrado->id ?? null,
            ]);
            return null;
        }
    }
}
