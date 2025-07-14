<?php

namespace App\Services\OrdemServico;

use App\Enum\OrdemServico\StatusOrdemServicoEnum;
use App\Enum\OrdemServico\TipoManutencaoEnum;
use App\Models\Agendamento;
use App\Models\OrdemServico;
use App\Services\NotificacaoService as notify;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgendamentoService
{

    protected OrdemServicoService   $ordemServicoService;
    protected int                   $veiculoId;

    public function __construct()
    {
        $this->ordemServicoService  = new OrdemServicoService();
    }

    public function create(array $data): ?Agendamento
    {
        return Agendamento::create($data);
    }

    public function gerarOrdemServico(Collection $agendamentos): void
    {
        dd($agendamentos);
        Log::debug('Iniciando geração de Ordem de Serviço a partir dos agendamentos.', [
            'agendamentos' => $agendamentos->pluck('id')->toArray(),
        ]);

        $this->veiculoId = $agendamentos->first()->veiculo_id;

        $agendamentos->each(function (Agendamento $agendamento) {
            if (! $this->validarAgendamento($agendamento)) {
                Log::error('Agendamento inválido: ' . $agendamento->id);
                notify::error('Agendamento inválido: ' . $agendamento->id);
                return;
            }
        });

        $ordemServico = $this->ordemServicoService->create([
            'veiculo_id'        => $this->veiculoId,
            'quilometragem'     => null,
            'tipo_manutencao'   => TipoManutencaoEnum::CORRETIVA,
            'data_inicio'       => now(),
            'status'            => StatusOrdemServicoEnum::PENDENTE,
            'status_sankhya'    => StatusOrdemServicoEnum::PENDENTE,
            'parceiro_id'       => $agendamentos->first()->parceiro_id ?? null,
            'created_by'        => Auth::user()->id,
        ]);

        Log::debug('Ordem de Serviço criada com sucesso.', [
            'ordem_servico_id' => $ordemServico->id,
        ]);

        $agendamentos->each(function (Agendamento $agendamento) use ($ordemServico) {
            Log::debug('Vinculando item agendado à OS.', [
                'agendamento_id'    => $agendamento->id,
                'ordem_servico_id'  => $ordemServico->id,
            ]);
            $this->vincularServico($agendamento, $ordemServico);
        });

    }

    //!precisa incluir validação
    public function vincularServico(Agendamento $agendamento, OrdemServico $ordemServico): void
    {
        $ordemServico->itens()->create([
            // 'ordem_servico_id'  => $this->ordemServico->id,
            'servico_id'        => $agendamento->servico_id,
            'posicao'           => $agendamento->posicao ?? null,
            'observacao'        => $agendamento->observacao,
            'status'            => StatusOrdemServicoEnum::PENDENTE,
            'created_by'        => Auth::user()->id,
            'updated_by'        => Auth::user()->id,
        ]);

        Log::debug('Serviço vinculado à OS.', [
            'agendamento_id'    => $agendamento->id,
            'ordem_servico_id'  => $ordemServico->id,
            'servico_id'        => $agendamento->servico_id,
        ]);

        $agendamento->update([
                'ordem_servico_id' => $ordemServico->id,
                'status'           => StatusOrdemServicoEnum::EXECUCAO,
                'updated_by'       => Auth::user()->id,
            ]);

        Log::debug('Agendamento atualizado com a OS vinculada.', [
            'agendamento_id'    => $agendamento->id,
            'ordem_servico_id'  => $ordemServico->id,
        ]);
    }

    protected function validarAgendamento(Agendamento $agendamento): bool
    {
        if ($agendamento->status != StatusOrdemServicoEnum::PENDENTE) {
            notify::error('Agendamento já está pendente.');
            return false;
        }

        if ($agendamento->ordem_servico_id) {
            notify::error('Agendamento já possui uma ordem de serviço vinculada.');
            return false;
        }

        if ($agendamento->veiculo_id != $this->veiculoId) {
            notify::error('Não é possível vincular agendamentos de veículos diferentes.');
            return false;
        }

        return true;
    }
}
