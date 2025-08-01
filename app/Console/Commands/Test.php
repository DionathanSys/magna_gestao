<?php

namespace App\Console\Commands;

use App\Services\PlanoManutencao\PlanoManutencaoService;
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
        $var = (new PlanoManutencaoService())->obterVencimentoPlanosPreventivos(2500);
        dd($var);
    }
}
