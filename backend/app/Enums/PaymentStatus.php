<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Collected = 'collected';
    case Disputed = 'disputed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Partial => 'Partiel',
            self::Collected => 'Encaissé',
            self::Disputed => 'Litige',
        };
    }
}
