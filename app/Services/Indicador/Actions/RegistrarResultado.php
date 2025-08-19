<?php

namespace App\Services\Indicador\Actions;

use App\Models;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrarResultado
{

    public function handle(array $data): Models\Resultado
    {
        $this->validate($data);
        return Models\Resultado::create($data);
    }

    private function validate(array $data)
    {
        Log::debug(__METHOD__, ['data' => $data]);

        Validator::make($data, [
            'gestor_id'         => 'required|exists:gestores,id',
            'indicador_id'      => 'required|exists:indicadores,id',
            'periodo'           => 'required|date_format:Y-m-d',
            'objetivo'          => 'required|numeric|min:0',
            'resultado'         => 'required|numeric|min:0.01',
            'pontuacao_obtida'  => 'required|numeric|min:0',
            'status'            => 'required|in:atingido,parcialmente_atingido,nao_atingido',
        ])->validate();

        $resultadoExistente = Models\Resultado::query()
            ->where('gestor_id', $data['gestor_id'])
            ->where('indicador_id', $data['indicador_id'])
            ->where('periodo', $data['periodo'])
            ->first();

        if ($resultadoExistente) {
            throw new \Exception('Resultado já existe para o gestor, indicador e período informados.');
        }
    }


}
