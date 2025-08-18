<?php

namespace App\Services\Indicador\Actions;

use App\Models;
use Illuminate\Support\Facades\Validator;

class RegistrarResultado
{
    protected Models\Indicador $indicador;
    protected float $pesoIndicador;

    public function __construct()
    {
        $this->indicador = new Models\Indicador();
    }

    public function handle(array $data): Models\Resultado
    {
        $this->validate($data);

        $data['pontuacao_obtida'] = $this->getPontuacaoResultado($data);
        $data['status'] = $this->getStatusResultado($data['pontuacao_obtida']);

        return Models\Resultado::create($data);
    }

    private function validate(array $data)
    {
        Validator::make($data, [
            'gestor_id'         => 'required|exists:gestores,id',
            'indicador_id'      => 'required|exists:indicadores,id',
            'periodo'           => 'required|date_format:Y-m-d',
            'objetivo'          => 'required|numeric|min:0',
            'resultado'         => 'required|numeric|min:0.01',
        ])->validate();
    }

    protected function getPontuacaoResultado(array $data): float
    {

        $pesoIndicador = $this->getPesoIndicador($data['indicador_id']);

        if ($data['resultado'] >= $data['objetivo']) {
            return $pesoIndicador;
        }

        return round(($pesoIndicador * $data['resultado']) / $data['objetivo'], 4);


        return 0;
    }

    protected function getPesoIndicador(int $indicadorId): float
    {
        $this->pesoIndicador = $this->indicador->findOrFail($indicadorId)->peso ?? 0;
        return $this->pesoIndicador;
    }

    protected function getStatusResultado(float $pontuacaoObtida): string
    {
        if ($pontuacaoObtida >= $this->pesoIndicador) {
            return 'atingido';
        }

        if ($pontuacaoObtida > 0) {
            return 'parcialmente_atingido';
        }

        return 'n_atingido';
    }
}
