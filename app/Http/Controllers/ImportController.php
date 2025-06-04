<?php

namespace App\Http\Controllers;

use App\Imports\ViagemImport;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importarViagens(Request $request)
    {
        new ViagemImport($request);
    }
}
