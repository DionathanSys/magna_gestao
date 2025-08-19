<?php

namespace App\Services\Indicador\Actions;

use Illuminate\Support\Facades\Validator;
use App\Models;
use Illuminate\Support\Facades\Log;

class CalculoPontuacaoResultado
{
    protected Models\Indicador $indicador;
    protected float $pesoIndicador;
    protected string $tipoAvaliacao;

    public function __construct()
    {
        $this->indicador = new Models\Indicador();
    }

    public function handle(array $data): array
    {
        $this->validate($data);

        $pontuacao_obtida = $this->getPontuacaoResultado($data);
        $status = $this->getStatusResultado($pontuacao_obtida);

        Log::debug(__METHOD__, [
            'data' => $data,
            'peso_indicador' => $this->pesoIndicador,
            'pontuacao_obtida' => $pontuacao_obtida,
            'status' => $status,
        ]);

        return [
            'pontuacao_obtida' => $pontuacao_obtida,
            'status' => $status
        ];
    }

    private function validate(array $data): void
    {
        Log::debug(__METHOD__, ['data' => $data]);

        Validator::make($data, [
            'indicador_id'  => 'required|exists:indicadores,id',
            'objetivo'      => 'required|numeric|min:0',
            'resultado'     => 'required|numeric|min:0',
        ])->validate();
    }

    protected function getPontuacaoResultado(array $data): float
    {
        $this->getIndicador($data['indicador_id']);
        $pesoIndicador = $this->pesoIndicador;

        if ($data['resultado'] == $data['objetivo']) {
            return $pesoIndicador;
        }

        if ($this->tipoAvaliacao === 'menor_melhor') {
            if ($data['resultado'] < $data['objetivo']) {
                return $pesoIndicador;
            } else {
                return 0;
            }
        } elseif ($this->tipoAvaliacao === 'maior_melhor') {
            if ($data['resultado'] > $data['objetivo']) {
                return $pesoIndicador;
            } else {
                return round(($pesoIndicador * $data['resultado']) / $data['objetivo'], 4);
            }
        }

        return 0;

    }

    protected function getIndicador(int $indicadorId): void
    {
        $indicador = $this->indicador->findOrFail($indicadorId);
        $this->pesoIndicador = $indicador->peso_por_periodo ?? 0;
        $this->tipoAvaliacao = $indicador->tipo_avaliacao;
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
