<?php

namespace App\Enum;

enum MotivoDivergenciaViagem: string
{
    case KM_ROTA_DIVERGENTE         = 'KM ROTA DIVERGENTE';
    case ERRO_DE_TRAJETO            = 'ERRO DE TRAJETO';
    case TRAJETO_NAO_PLANEJADO      = 'TRAJETO NÃO PLANEJADO';
    case DESVIO_JUSTIFICADO         = 'DESVIO JUSTIFICADO';
    case DESVIO_NAO_JUSTIFICADO     = 'DESVIO NÃO JUSTIFICADO';
    case DESLOCAMENTO_GARAGEM       = 'DESLOCAMENTO GARAGEM';
    case DESLOCAMENTO_OUTROS        = 'DESLOCAMENTO OUTROS';
    case RETORNO_VEICULO_QUEBRADO   = 'RETORNO VEÍCULO QUEBRADO';

    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($item) => [$item->value => $item->value])
            ->toArray();
    }
}
