<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LaraDumps\LaraDumps\Livewire\Attributes\Ds;

class ConsultarVencimentoPlanosPreventivos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consultar-vencimento-planos-preventivos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consultar/Agendar planos preventivos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $planosPreventivos = \App\Models\PlanoPreventivo::query()
            ->select('id', 'descricao', 'intervalo', 'itens')
            ->where('is_active', true)
            ->get();

        foreach ($planosPreventivos as $plano) {
            $consultarPlanos = new \App\Services\PlanoManutencao\ConsultarPrevisaoPlanos($plano->id, $plano->intervalo, 2500);
            $previsaoPlanos[$plano->id . ' - ' . $plano->descricao] = $consultarPlanos->exec();
        }


        ds($previsaoPlanos)->label('Geral');
    }
}
