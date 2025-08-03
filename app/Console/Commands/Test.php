<?php

namespace App\Console\Commands;

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
        $data = [
            'veiculo_id' =>'r',
            'servico_id' => 3,
        ];

        $agendamento = (new AgendamentoService())->create($data);

        if ($agendamento->hasError()){
            notify::error('Erro ao criar agendamento.', $agendamento->getMessage());
            return;
        }

        notify::success('Agendamento criado com sucesso!');
        return $agendamento;
    }
}
