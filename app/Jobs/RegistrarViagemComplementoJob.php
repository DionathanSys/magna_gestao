<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Viagem;
use App\Services\Viagem\ViagemComplementoService;
use Illuminate\Support\Facades\Log;

class RegistrarViagemComplementoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Viagem $viagem)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('Iniciando o registro do complemento da viagem: ' . $this->viagem->id);
        (new ViagemComplementoService)->create($this->viagem);
    }
}
