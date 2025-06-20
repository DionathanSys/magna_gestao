<?php

namespace App\Enum\Pneu;

enum LocalPneuEnum: string
{
    case ESTOQUE_CCO = 'ESTOQUE_CCO';
    case ESTOQUE_CTV = 'ESTOQUE_CTV';
    case MANUTENCAO  = 'MANUTENÇÃO';
    case SUCATA      = 'SUCATA';

    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($item) => [$item->name => $item->value])
            ->toArray();
    }
}
