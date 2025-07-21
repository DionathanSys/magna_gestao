<?php

use App\Http\Controllers\ImportController;
use App\Models\OrdemServico;
use App\Models\PlanoManutencaoVeiculo;
use App\Models\Pneu;
use App\Models\PneuPosicaoVeiculo;
use App\Models\Veiculo;
use App\Models\Viagem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('import')->group(function () {

    /**
     * Importação de Viagens
     */

    Route::view('/viagem', 'import.viagem.importFile', [
        'title' => 'Importar Viagens',
        'route' => 'store.viagens'
    ])->name('import.viagens');

    Route::post('/viagem', [ImportController::class, 'importarViagens'])->name('store.viagens');

    /**
     * Importação de Integrados
     */

    Route::view('/integrado', 'import.integrado.importFile', [
        'title' => 'Importar Integrados',
        'route' => 'store.integrados'
    ]);

    Route::post('/integrado', [ImportController::class, 'importIntegrados'])->name('store.integrados');

    /**
     * Importação de documentos de frete
     */

    Route::view('/documento-frete', 'import.documentofrete.importFile', [
        'title' => 'Importar Documento de Frete',
        'tipo_documento' => \App\Enum\Frete\TipoDocumentoEnum::toSelectArray(),
        'route' => 'store.documentofrete'
    ]);

    Route::post('/documentofrete', [ImportController::class, 'importarDocumentoFrete'])->name('store.documentofrete');
});

Route::get('/teste', function () {

    $viagensComDispersao = Viagem::with(['cargas.integrado'])
        ->get()
        ->filter(function ($viagem) {
            return ($viagem->km_rodado - $viagem->km_pago) > 3.5
            //  && $viagem->numero_viagem == '21052347'
             ;
        })
        ->map(function ($viagem) {
            return [
                'nro_viagem'            => $viagem->numero_viagem,
                'doc_transp'            => $viagem->documento_transporte,
                'km_rodado'             => $viagem->km_rodado,
                'km_pago'               => $viagem->km_pago,
                'km_disperso'           => $viagem->km_rodado - $viagem->km_pago,
                'motivo_divergencia'    => $viagem->motivo_divergencia->value ?? 'N/A',
                'num_destinos'          => $viagem->cargas->pluck('integrado_id')->unique()->count(),
                'destinos'              => $viagem->cargas
                    ->pluck('integrado.nome') 
                    ->unique()
                    ->implode(', '),
            ];
        })->values()->all();

    // Cria a planilha
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Cabeçalho
    $headers = [
        'Nº Viagem', 'Doc. Transporte', 'KM Rodado', 'KM Pago', 'KM Disperso', 
        'Motivo Divergência', 'Nº Destinos', 'Destinos'
    ];
    $sheet->fromArray($headers, null, 'A1');

    // Dados
    $row = 2;
    foreach ($viagensComDispersao as $viagem) {
        $sheet->fromArray(array_values($viagem), null, 'A' . $row);
        $row++;
    }

    // Download
    $writer = new Xlsx($spreadsheet);
    $filename = 'viagens_dispersao.xlsx';

    // Cabeçalhos para download
    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
        'Pragma' => 'public',
    ]);

});


