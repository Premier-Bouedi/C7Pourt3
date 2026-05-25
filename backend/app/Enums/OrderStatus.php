<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case ShippedMorocco = 'shipped_morocco';
    case ArrivedGabon = 'arrived_gabon';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmé',
            self::ShippedMorocco => 'Expédié (Maroc)',
            self::ArrivedGabon => 'Arrivé au Gabon',
            self::Delivered => 'Livré',
            self::Cancelled => 'Annulé',
        };
    }
}
