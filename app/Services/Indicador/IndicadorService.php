<?php

namespace App\Services\Indicador;

use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models;

class IndicadorService
{

    use ServiceResponseTrait;

    public function createResultadoColetivo(array $data): bool
    {
        try {

            $gestores = Models\Gestor::query()
                ->whereHas('indicadores', function ($query) use ($data) {
                    $query->where('indicador_id', $data['indicador_id']);
                })->get();

            Log::debug(__METHOD__, [
                'gestores' => $gestores->get(),
                'data' => $data,
            ]);

            $gestores->each(function ($gestor) use (&$data) {
                $data['gestor_id'] = $gestor->id;
                $this->createResultado($data);
            });

            $this->setSuccess('Resultado coletivo criado com sucesso!');
            return true;
        } catch (\Exception $e) {
           $this->setError($e->getMessage());
           return false;
        }
    }

    public function createResultado(array $data): ?\App\Models\Resultado
    {
        try {
            $pontucao = (new Actions\CalculoPontuacaoResultado())->handle($data);
            $data = array_merge($data, $pontucao);
            $resultado = (new Actions\RegistrarResultado())->handle($data);

            Log::debug(__METHOD__,[
                'resultado' => $resultado,
            ]);

            $this->setSuccess('Resultado criado com sucesso!');
            return $resultado;
        } catch (\Exception $e) {
           $this->setError($e->getMessage());
           return null;
        }
    }
}
