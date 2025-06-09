<?php

namespace App\Services;

use App\DTO\ViagemDTO;
use App\Models\Integrado;
use App\Models\Viagem;

class ViagemService
{
    public Viagem           $viagem;
    public CargaService     $cargaService;

    public function __construct()
    {
        $this->viagem = new Viagem();
        $this->cargaService = new CargaService();
    }

    public function create(ViagemDTO $viagemDto)
    {
        try {

            $viagem = $this->viagem
                            ->where('numero_viagem', $viagemDto->numero_viagem)
                            ->first();

            if ($viagem && $viagem->conferido == false) {
                $viagem->update($viagemDto->toArray());
            } else {
                $viagem = $this->viagem->create($viagemDto->toArray());
            }

            $carga = $this->cargaService->create($viagemDto->integrado, $viagem);

            return $viagem;

        } catch (\Exception $e) {
            dd($viagem, $e);
            return $e;
        }

    }

    public function verificaDivergencia(Viagem $viagem): array
    {
        $divergencias = [];

        if ($viagem->km_divergencia > 1) {
            $divergencias['km_divergencia'] = $viagem->km_divergencia;
        }

        if (! $viagem->documento_transporte) {
            $divergencias['documento_transporte'] = 'Documento de transporte não informado';
        }

        if (! $viagem->integrado) {
            $divergencias['integrado'] = 'Integrado não informado';
        }

        if (! $viagem->km_cadastro) {
            $divergencias['km_cadastro'] = 'KM de cadastro não informado';
        }

        //TODO: Implementar modo de atualizar a viagem com as divergências encontradas

        return $divergencias;
    }

}
