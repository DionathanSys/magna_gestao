<?php

namespace App\Console\Commands;

use App\Models\ItemOrdemServico;
use App\Services\Agendamento\AgendamentoService;
use App\Services\PlanoManutencao\PlanoManutencaoService;
use App\Services\NotificacaoService as notify;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $item = ItemOrdemServico::query()
            ->where('ordem_servico_id', 148)
            ->with(['ordemServico', 'agendamento'])
            ->get();

            ds($item)->label('Item Ordem de Serviço');
            ds($item->first()->agendamento)->label('Agendamento do Item Ordem de Serviço');

    }
}
