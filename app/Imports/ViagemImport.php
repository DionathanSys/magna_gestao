<?php

namespace App\Imports;

use App\DTO\ViagemDTO;
use App\Models\CargaViagem;
use App\Models\Integrado;
use App\Models\Veiculo;
use App\Models\Viagem;
use App\Services\ViagemService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ViagemImport
{
    protected $index;
    protected $data;
    protected $dataCompetencia;
    protected $veiculos;
    protected array $cargas;

    public function __construct(Request $request)
    {
        $read                   = IOFactory::load($request->file);
        $this->data             = $read->getActiveSheet()->toArray();
        $this->index            = array_flip($this->data[0]);
        $this->dataCompetencia  = $request->all()['dataCompetencia'];
        $this->veiculos         = Veiculo::all()->pluck('id', 'placa')->toArray();
        unset($this->data[0]); // Remove header row
    }

    public function store()
    {
        foreach ($this->data as $key => $row) {

            $dataFim = Carbon::createFromFormat('d/m/Y H:i', $row[$this->index['Fim']])->format('Y-m-d');
            if ($dataFim == $this->dataCompetencia) {

                $viagemDto = ViagemDTO::makeFromArray(
                    [
                        'numero_viagem'         => $row[$this->index['Viagem']],
                        'documento_transporte'  => $row[$this->index['Carga Cliente']] ?? null,
                        'integrado'             => $row[$this->index['Destino']] ?? null,
                        'veiculo_id'            => $this->veiculos[$row[$this->index['Placa']]],
                        'km_rodado'             => $row[$this->index['Km Rodado']] ?? 0,
                        'km_pago'               => $row[$this->index['Km Sugerida']] ?? 0,
                        'data_competencia'      => $this->dataCompetencia,
                        'data_inicio'           => $row[$this->index['Inicio']],
                        'data_fim'              => $row[$this->index['Fim']],
                        'created_by'            => Auth::user()->id,
                        'updated_by'            => Auth::user()->id,
                    ]
                );

                $viagem = (new ViagemService)->create($viagemDto);
            }
        }
    }

    private function extrairNomeIntegrado($nome)
    {
        $nomeFinal = preg_replace('/\s*\(.*$/', '', $nome);
        return $nomeFinal;
    }
}
