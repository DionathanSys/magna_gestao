<?php

use App\Http\Controllers\ImportController;
use App\Services\IntegradoService;
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
        'route' => 'import.viagens'
    ]);

    Route::post('/viagem', [ImportController::class, 'importarViagens'])->name('import.viagens');

    /**
     * Importação de Integrados
     */

    Route::view('/integrado', 'import.integrado.importFile', [
        'title' => 'Importar Integrados',
        'route' => 'import.integrados'
    ]);

    Route::post('/integrado', [ImportController::class, 'importIntegrados'])->name('import.integrados');

    /**
     * Importação de documentos de frete
     */

    Route::view('/documento-frete', 'import.documentofrete.importFile', [
        'title' => 'Importar Documento de Frete',
        'tipo_documento' => \App\Frete\TipoDocumentoEnum::toSelectArray(),
        'route' => 'import.documentofrete'
    ]);

    Route::post('/documentofrete', [ImportController::class, 'importarDocumentoFrete'])->name('import.documentofrete');

});

Route::get('/teste', function () {
    $service = new IntegradoService();
    $integrado = $service->buscaIntegrado('LAURI ANTONIO FUNKLER E OU LUC (729868 _STP1,2)');
    dd($integrado);


})->name('teste');
