<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class CalcularQuilometragemMediaVeiculo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calcular-quilometragem-media-veiculo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcular e atualizar a quilometragem média de todos os veículos ativos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $veiculos = \App\Models\Veiculo::query()
            ->select('id', 'placa')
            ->where('is_active', true)
            ->get();

        foreach ($veiculos as $veiculo) {

            //Calcular a média de quilometragem do veículo
            $calcularKmMedio = new \App\Services\Veiculo\CalcularKmMedio($veiculo->id);
            $kmMedio = $calcularKmMedio->exec();

            if($kmMedio == null) {
                $this->error("Não foi possível calcular a quilometragem média para o veículo {$veiculo->placa}.");
                continue;
            }

            //Atualizar a média de quilometragem do veículo
            $atualizarKmMedio = new \App\Services\Veiculo\AtualizarKmMedio($veiculo->id);
            $atualizarKmMedio->exec($kmMedio);

        }
    }
}
