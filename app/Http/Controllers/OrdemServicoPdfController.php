<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Services\OrdemServico\OrdemServicoPdfService;
use Illuminate\Http\Request;

class OrdemServicoPdfController extends Controller
{
    public function __construct(
        private OrdemServicoPdfService $ordemServicoPdfService
    ) {}

    /**
     * Visualiza o PDF da Ordem de Serviço no navegador
     */
    public function visualizar(OrdemServico $ordemServico)
    {
        return $this->ordemServicoPdfService->visualizarPdfOrdemServico($ordemServico);
    }

    /**
     * Faz download do PDF da Ordem de Serviço
     */
    public function download(OrdemServico $ordemServico)
    {
        return $this->ordemServicoPdfService->gerarPdfOrdemServico($ordemServico);
    }

    /**
     * Visualiza o PDF da Ordem de Serviço com Tailwind CSS no navegador
     */
    public function visualizarTailwind(OrdemServico $ordemServico)
    {
        return $this->ordemServicoPdfService->visualizarPdfOrdemServicoTailwind($ordemServico);
    }

    /**
     * Faz download do PDF da Ordem de Serviço com Tailwind CSS
     */
    public function downloadTailwind(OrdemServico $ordemServico)
    {
        return $this->ordemServicoPdfService->gerarPdfOrdemServicoTailwind($ordemServico);
    }
}
