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

    protected int $veiculoId;
    protected int $parceiroId;


    public function incluirAgendamentosEmOrdemServico(Collection $agendamentos)
    {
        $this->veiculoId    = $agendamentos->first()->veiculo_id;
        $this->parceiroId   = $agendamentos->first()->parceiro_id;

        $agendamentos->each(function (Agendamento $agendamento) {
            if (! $this->validarAgendamento($agendamento)) {
                Log::error('Agendamento inválido: ' . $agendamento->id);
                notify::error('Agendamento inválido: ' . $agendamento->id);
                return;
            }
        });

        $ordemServico = OrdemServico::query()->updateOrCreate(
            [
                'veiculo_id'    => $this->veiculoId,
                'status'        => StatusOrdemServicoEnum::PENDENTE,
                'parceiro_id'   => $agendamentos->first()->parceiro_id,
            ],
            [
                'veiculo_id'        => $this->veiculoId,
                'quilometragem'     => null,
                'tipo_manutencao'   => TipoManutencaoEnum::CORRETIVA,
                'data_inicio'       => now(),
                'status'            => StatusOrdemServicoEnum::PENDENTE,
                'status_sankhya'    => StatusOrdemServicoEnum::PENDENTE,
                'parceiro_id'       => $agendamentos->first()->parceiro_id,
                'created_by'        => Auth::user()->id,
            ]
        );

         $agendamentos->each(function (Agendamento $agendamento) use ($ordemServico) {
            Log::debug('Vinculando item agendado à OS.', [
                'agendamento_id'    => $agendamento->id,
                'ordem_servico_id'  => $ordemServico->id,
            ]);
            $this->vincularServico($agendamento, $ordemServico);
        });

        notify::success(mensagem: 'Agendamentos vinculados à Ordem de Serviço com sucesso.');

    }

    protected function vincularServico(Agendamento $agendamento, OrdemServico $ordemServico): void
    {
        $ordemServico->itens()->create([
            // 'ordem_servico_id'  => $this->ordemServico->id,
            'servico_id'        => $agendamento->servico_id,
            'posicao'           => $agendamento->posicao ?? null,
            'observacao'        => $agendamento->observacao,
            'status'            => StatusOrdemServicoEnum::PENDENTE,
            'created_by'        => Auth::user()->id,
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

        if ($agendamento->parceiro_id != $this->parceiroId) {
            notify::error('Não é possível vincular agendamentos de parceiros externos diferentes.');
            return false;
        }

        return true;
    }
}
