<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case ShippedMorocco = 'shipped_morocco';
    case EnCoursLivraison = 'en_cours_de_livraison';
    case ArrivedGabon = 'arrived_gabon';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmé',
            self::ShippedMorocco => 'Expédié (Maroc)',
            self::EnCoursLivraison => 'En cours de livraison',
            self::ArrivedGabon => 'Arrivé au Gabon',
            self::Delivered => 'Livré',
            self::Cancelled => 'Annulé',
        };
    }
}

