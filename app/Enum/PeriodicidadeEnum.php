<?php

namespace App\Enum;

enum PeriodicidadeEnum: string
{
    case MENSAL = 'MENSAL';
    case BIMESTRAL = 'BIMESTRAL';
    case TRIMESTRAL = 'TRIMESTRAL';
    case SEMESTRAL = 'SEMESTRAL';
    case ANUAL = 'ANUAL';

    public function label(): string
    {
        return match ($this) {
            self::MENSAL => 'Mensal',
            self::BIMESTRAL => 'Bimestral',
            self::TRIMESTRAL => 'Trimestral',
            self::SEMESTRAL => 'Semestral',
            self::ANUAL => 'Anual',
        };
    }

    public function periodicidadeAno(): int
    {
        return match ($this) {
            self::MENSAL => 12,
            self::BIMESTRAL => 6,
            self::TRIMESTRAL => 4,
            self::SEMESTRAL => 6,
            self::ANUAL => 1,
        };
    }
}

