<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case Bank = 'bank';
    case Qris = 'qris';

    public function label(): string
    {
        return match ($this) {
            self::Bank => 'Bank Transfer',
            self::Qris => 'QRIS',
        };
    }
}
