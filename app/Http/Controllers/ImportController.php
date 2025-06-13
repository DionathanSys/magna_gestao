<?php

namespace App\Http\Controllers;

use App\DTO\DocumentoFreteDTO;
use App\DTO\ViagemDTO;
use App\Frete\TipoDocumentoEnum;
use App\Imports\IntegradoImport;
use App\Imports\ViagemImport;
use App\Models\Veiculo;
use App\Services\DocumentoFreteService;
use App\Services\IntegradoService;
use App\Services\ViagemService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function importarViagens(Request $request)
    {
        try {

            $read             = IOFactory::load($request->file);
            $data             = collect($read->getActiveSheet()->toArray());
            $dataCorte        = $request->input('data_corte');

            (new ViagemService)->processarImportacao($data, $dataCorte);

        } catch (\Exception $e) {
            Log::error("Erro ao importar viagens", [
                'metodo' => __METHOD__. ' - ' . __LINE__,
                'mensagem' => $e->getMessage(),
            ]);
            dd('Erro ao importar viagens: ' . $e->getMessage());
        }

    }

    public function importIntegrados(Request $request)
    {
        (new IntegradoImport($request))->store();
    }

    public function importarDocumentoFrete(Request $request)
    {
        try {

            $read           = IOFactory::load($request->file);
            $data           = $read->getActiveSheet()->toArray();
            $index          = array_flip($data[2]);
            $tipoDocumento  = $request->input('tipo_documento');
            $veiculos         = Veiculo::all()->pluck('id', 'placa')->toArray();
            unset($data[0], $data[1], $data[2]); // Remove row

            foreach ($data as $row) {

                if($tipoDocumento == TipoDocumentoEnum::CTE->value){
                    $documentoTransporte = preg_match('/Transporte:\s*(\d+)/', $row[$index['Observação']], $matches) ? $matches[1] : null;
                    $destino = $row[$index['Nome + UF (Cidade Fim CT-e)']];
                } else {
                    $documentoTransporte = $row[$index['Documento Transporte']] ?? null;
                }

                $placa = str_replace(['[', ']'], '', $row[$index['Marca [Placa] (Veículos)']]);
                $veiculoId = isset($placa) ? $veiculos[$placa] ?? null : null;

                $documentoDto = DocumentoFreteDTO::makeFromArray(
                    [
                        'veiculo_id'            => $veiculoId,
                        'documento_transporte'  => $documentoTransporte,
                        'numero_documento'      => $row[$index['Nro. Nota']] ?? null,
                        'tipo_documento'        => $tipoDocumento,
                        'data_emissao'          => Carbon::createFromFormat('d/m/Y', $row[$index['Dt. Neg.']])->format('Y-m-d') ?? null,
                        'valor_total'           => (float) str_replace(',', '.', str_replace('.', '', $row[$index['Vlr. Nota']] ?? 0)),
                        'valor_icms'            => (float) str_replace(',', '.', str_replace('.', '', $row[$index['Vlr. do ICMS']] ?? 0)),
                        'destino'               => $destino ?? null,
                    ]
                );

                $documento = new DocumentoFreteService();
                $documento->create($documentoDto);

            }

        } catch (\Exception $e) {
            Log::alert("Erro ao importar documento de frete: " . $e->getMessage());
            dd('Erro ao importar documento de frete: ' . $e->getMessage());
        }
    }
}
