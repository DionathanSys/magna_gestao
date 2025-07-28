<?php

namespace App\Http\Controllers;

use App\Services\PlanoManutencao\PlanoManutencaoService;
use Illuminate\Http\Request;

class PlanoManutencaoController extends Controller
{
    public function __construct(
        private PlanoManutencaoService $planoManutencaoService
    ) {}

    /**
     * Exibe a lista de planos com vencimento próximo
     */
    public function index(Request $request)
    {
        $kmTolerancia = $request->get('km_tolerancia', 2500);

        $planos = $this->planoManutencaoService->obterVencimentoPlanosPreventivos($kmTolerancia);

        return view('planos-manutencao.index', compact('planos', 'kmTolerancia'));
    }

    /**
     * Gera e faz download do PDF com os planos de vencimento
     */
    public function downloadPdf(Request $request)
    {
        $kmTolerancia = $request->get('km_tolerancia', 2500);

        return $this->planoManutencaoService->gerarRelatorioVencimentoPdf($kmTolerancia);
    }

    /**
     * Visualiza o PDF no navegador
     */
    public function visualizarPdf(Request $request)
    {
        $kmTolerancia = $request->get('km_tolerancia', 2500);

        return $this->planoManutencaoService->visualizarRelatorioVencimentoPdf($kmTolerancia);
    }
}
