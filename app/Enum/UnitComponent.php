<?php

namespace App\Enum;

enum UnitComponent: string
{
    case IBUILD = 'IBUILD';
    case IREAP = 'IREAP';
    case IPLAN = 'IPLAN';
    case GGU = 'GGU';
    case SES = 'SES';
    case MEL = 'MEL';
    case INFOACE = 'INFOACE';
    case PROCUREMENT = 'PROCUREMENT';
    case FINANCE = 'FINANCE';
    case IDU = 'IDU';
    case ADMIN = 'ADMIN';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
