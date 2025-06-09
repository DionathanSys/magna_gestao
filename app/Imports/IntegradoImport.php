<?php

namespace App\Imports;

use App\Models\Integrado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IntegradoImport
{
    protected $index;
    protected $data;

    public function __construct(Request $request)
    {
        $read       = IOFactory::load($request->file);
        $this->data = $read->getActiveSheet()->toArray();
        unset($this->data[0]); // Remove header row

    }

    public function store()
    {
        $data = collect($this->data);

        DB::transaction(function () use ($data) {
            $data->each(function ($row) {

                $integrado = [
                    'codigo'    => $row[0],
                    'nome'      => $row[1],
                    'km_rota'   => $row[2] ?? 0,
                    'municipio' => $row[3] ?? null,
                    'estado'    => $row[4] ?? null,
                ];

                Integrado::updateOrCreate(
                    ['codigo' => $integrado['codigo']],
                    $integrado
                );

            });
        });

    }
}
