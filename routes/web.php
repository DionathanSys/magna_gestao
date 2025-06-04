<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('import')->group(function () {

    Route::view('/viagens', 'importFile', [
        'title' => 'Importar Viagens',
        'route' => 'import.viagens'
    ]);

    Route::post('/viagens', [ImportController::class, 'importarViagens'])->name('import.viagens');
    
});
