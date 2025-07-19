<?php

use App\Http\Controllers\ImportController;
use App\Models\OrdemServico;
use App\Models\PlanoManutencaoVeiculo;
use App\Models\Pneu;
use App\Models\PneuPosicaoVeiculo;
use App\Models\Veiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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

    $model = PlanoManutencaoVeiculo::find(1);
    echo $model->veiculo_id;

});


// Route::get('/teste', function () {
//     // $veiculos = [
//     //     'RLP7B55',
//     //     'RXL3I85',
//     //     'RYD2D62',
//     //     'SXV0G23',
//     //     'SXW4C78',
//     //     'SXA6B49',
//     //     'RKY9I86',
//     //     'SXA6A99',
//     //     'SXQ3B53',
//     //     'RKY6J30',
//     //     'SXZ8F62',
//     //     'SXA9H64',
//     //     'RXV0C84',
//     //     'RYB9C55',
//     //     'RXO3I55',
//     //     'RXX3C16',
//     //     'RXV5H78',
//     //     'RLO2C88',
//     //     'RXP5E89',
//     //     'RXN9G70',
//     //     'RDX9D24',
//     //     'SXJ6G77',
//     // ];

//     // foreach ($veiculos as $value) {
//     //     $d = Veiculo::create([
//     //         'placa' => $value,
//     //     ]);
//     // }
//     // $veiculo = Veiculo::find(1);
//     // $pneus = $veiculo->pneus;
//     // dd($veiculo, $pneus);

//     $veiculos = Veiculo::all();

//     $veiculos->each(function ($veiculo) {

//         $data = [
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 1,
//                 'posicao'       => 'DD',
//                 'sequencia'    => 1,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 1,
//                 'posicao'       => 'DE',
//                 'sequencia'    => 2,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 2,
//                 'posicao'       => '2DD',
//                 'sequencia'    => 3,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 2,
//                 'posicao'       => '2DE',
//                 'sequencia'    => 4,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 3,
//                 'posicao'       => 'TDE',
//                 'sequencia'    => 5,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 3,
//                 'posicao'       => 'TDI',
//                 'sequencia'    => 6,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 3,
//                 'posicao'       => 'TEI',
//                 'sequencia'    => 7,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 3,
//                 'posicao'       => 'TEE',
//                 'sequencia'    => 8,
//             ],
//              [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 4,
//                 'posicao'       => '2TDE',
//                 'sequencia'    => 9,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 4,
//                 'posicao'       => '2TDI',
//                 'sequencia'    => 10,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 4,
//                 'posicao'       => '2TEI',
//                 'sequencia'    => 11,
//             ],
//             [
//                 'veiculo_id'    => $veiculo->id,
//                 'data_inicial'  => null,
//                 'km_inicial'    => null,
//                 'eixo'          => 4,
//                 'posicao'       => '2TEE',
//                 'sequencia'    => 12,
//             ],
//         ];

//         foreach ($data as $item) {

//             // Cria a posição do pneu no veículo
//             PneuPosicaoVeiculo::create($item);
//         }

//     });
// })->name('teste');
