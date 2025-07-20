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

dd(1);  
});


