<?php

namespace App\Imports;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ViagemImport
{
    protected $data = [];

    public function ___construct(Request $request)
    {
        $read = IOFactory::load($request->file);
        $this->data = $read->getActiveSheet()->toArray();
        ds($this->data);
    }
}
