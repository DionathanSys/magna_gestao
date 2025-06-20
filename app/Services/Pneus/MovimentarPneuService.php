<?php

namespace App\Services\Pneus;

use App\Enum\Pneu\MotivoMovimentoPneuEnum;
use App\Models\HistoricoMovimentoPneu;
use App\Models\PneuPosicaoVeiculo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class MovimentarPneuService
{

    protected HistoricoMovimentoPneu $historicoMovimentoPneu;

    public function __construct()
    {
        $this->historicoMovimentoPneu = new HistoricoMovimentoPneu();
    }


    public function removerPneu(PneuPosicaoVeiculo $pneuVeiculo, array $data)
    {

        if ($pneuVeiculo->km_inicial > $data['km_final']) {
            throw new \Exception('A KM final não pode ser maior que a KM inicial.');
        }

        $this->historicoMovimentoPneu->create([
            'pneu_id'           => $pneuVeiculo->pneu_id,
            'veiculo_id'        => $pneuVeiculo->veiculo_id,
            'data_inicial'       => $pneuVeiculo->data_inicial,
            'km_inicial'        => $pneuVeiculo->km_inicial,
            'eixo'              => $pneuVeiculo->eixo,
            'posicao'           => $pneuVeiculo->posicao,
            'motivo'            => $data['motivo'],
            'sulco_movimento'   => $data['sulco'],
            'data_final'        => $data['data_final'],
            'km_final'          => $data['km_final'],
            'observacao'        => $data['observacao'],
        ]);

        $pneuVeiculo->update([
            'pneu_id'       => null,
            'data_inicial'  => null,
            'km_inicial'    => null,
        ]);
    }


    public function aplicarPneu(PneuPosicaoVeiculo $pneuVeiculo, array $data)
    {

        $pneuVeiculo->update([
            'pneu_id'       => $data['pneu_id'],
            'data_inicial'  => $data['data_inicial'],
            'km_inicial'    => $data['km_inicial'],
        ]);
    }

    public function trocarPneu(PneuPosicaoVeiculo $pneuVeiculo, array $data)
    {

        $this->removerPneu($pneuVeiculo, [
            'data_final' => $data['data_inicial'],
            'km_final'   => $data['km_inicial'],
            'sulco'      => $data['sulco'] ?? 0,
            'motivo'     => $data['motivo'],
            'observacao' => $data['observacao'] ?? null,
        ]);

        $this->aplicarPneu($pneuVeiculo, [
            'pneu_id'       => $data['pneu_id'],
            'data_inicial'  => $data['data_inicial'],
            'km_inicial'    => $data['km_inicial'],
        ]);
    }

    public function rodizioPneu(Collection $pneusVeiculo, array $data)
    {
        if ($pneusVeiculo->isEmpty() && $pneusVeiculo->count() == 2) {
            return;
        }

        $pneusId = $pneusVeiculo->pluck('pneu_id')->toArray();

        $pneusVeiculo->each(function (PneuPosicaoVeiculo $pneuVeiculo) use ($pneusId, $data) {

            $pneuId = Arr::where($pneusId, function ($id) use ($pneuVeiculo) {
                return $id !== $pneuVeiculo->pneu_id;
            });

            $pneuId = Arr::first($pneuId);

            $this->removerPneu($pneuVeiculo, [
                'data_final' => $data['data_inicial'],
                'km_final'   => $data['km_inicial'],
                'sulco'      => $data['sulco'] ?? 0,
                'observacao' => $data['observacao'] ?? null,
                'motivo'     => $data['motivo'] ?? MotivoMovimentoPneuEnum::RODIZIO->value,
            ]);

            $this->aplicarPneu($pneuVeiculo, [
                'pneu_id'       => $pneuId,
                'data_inicial'  => $data['data_inicial'],
                'km_inicial'    => $data['km_inicial'],
            ]);

        });
    }


}
