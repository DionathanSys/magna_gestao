<?php

namespace App\Services\Veiculo;

class VeiculoService
{
    public function getKmAtualVeiculos(array $veiculos): array
    {
        $veiculos = \App\Models\Veiculo::query()
            ->select('id', 'placa')
            ->with('kmAtual')
            ->whereIn('id', $veiculos)
            ->get();

        $resultado = [];

        foreach ($veiculos as $veiculo) {
            $resultado[$veiculo->id] = [
                'km_atual' => $veiculo->kmAtual?->quilometragem ?? 0,
                'placa' => $veiculo->placa,
            ];
        }

        return $resultado;
    }
}
