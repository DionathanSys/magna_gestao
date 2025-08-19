<?php

namespace App\Services\Indicador;

use App\Traits\ServiceResponseTrait;

class IndicadorService
{

    use ServiceResponseTrait;
    // This service can be used to manage indicators, such as creating, updating, and retrieving them.
    // It can also include methods for calculating scores or statuses based on the indicators.

    // Example method to create an indicator
    public function createIndicator(array $data)
    {
        // Logic to create an indicator
    }

    // Example method to update an indicator
    public function updateIndicator(int $id, array $data)
    {
        // Logic to update an indicator
    }

    // Example method to retrieve indicators
    public function getIndicators()
    {
        // Logic to retrieve indicators
    }

    public function createResultado(array $data)
    {
        try {
            $pontucao = (new Actions\CalculoPontuacaoResultado())->handle($data);
            $data = array_merge($data, $pontucao);
            $resultado = (new Actions\RegistrarResultado())->handle($data);
            $this->setSuccess('Resultado criado com sucesso!');
            return $resultado;
        } catch (\Exception $e) {
           $this->setError($e->getMessage());
           return null;
        }
    }
}
