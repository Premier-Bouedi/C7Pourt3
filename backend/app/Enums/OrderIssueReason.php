<?php

namespace App\Enums;

enum OrderIssueReason: string
{
    case Delay = 'delay';
    case Damaged = 'damaged';
    case WrongAddress = 'wrong_address';
    case Unreachable = 'unreachable';
    case Stock = 'stock';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Delay => 'Retard de livraison',
            self::Damaged => 'Produit endommagé',
            self::WrongAddress => 'Adresse incorrecte',
            self::Unreachable => 'Client injoignable',
            self::Stock => 'Problème de stock',
            self::Other => 'Autre',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $c) => [$c->value => $c->label()],
        )->all();
    }
}
