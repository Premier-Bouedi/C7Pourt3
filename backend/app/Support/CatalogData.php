<?php

namespace App\Support;

/**
 * Catalogue officiel C7Pourt3 — 10 sacs.
 */
class CatalogData
{
    public const STOCK_MOROCCO = 15;

    public const STOCK_VARIANT = 10;

    /** @return array<int, array{name: string, slug: string, category: string, price: int, image: string, color: string, color_hex: string, featured: bool}> */
    public static function products(): array
    {
        return [
            ['Sac Croco Noir — Chaîne Dorée', 'sac-croco-noir-chaine-doree', 'soiree', 89000, 'sac-01.png', 'Noir', '#1a1a1a', true],
            ['Sac Dôme Texturé Noir', 'sac-dome-texture-noir', 'quotidien', 72000, 'sac-02.png', 'Noir', '#1a1a1a', false],
            ['Sac Matelassé Métallisé', 'sac-matelasse-metallise', 'soiree', 95000, 'sac-03.png', 'Gris métallisé', '#6b7280', true],
            ['Sac Croco Noir — Fermoir Argent', 'sac-croco-noir-fermoir-argent', 'soiree', 92000, 'sac-04.png', 'Noir', '#1a1a1a', true],
            ['Sac Satchel Bleu Royal', 'sac-satchel-bleu-royal', 'quotidien', 68000, 'sac-05.png', 'Bleu royal', '#1e3a8a', false],
            ['Sac Fourrure Crème — Bandoulière', 'sac-fourrure-creme', 'luxe', 125000, 'sac-06.png', 'Crème', '#f5f5dc', true],
            ['Sac Tote Texturé Marron', 'sac-tote-marron', 'quotidien', 58000, 'sac-07.png', 'Marron', '#78350f', false],
            ['Sac Bandoulière Monogramme', 'sac-bandouliere-monogramme', 'bandouliere', 110000, 'sac-08.png', 'Monogramme', '#92400e', true],
            ['Sac Speedy Monogramme Classique', 'sac-speedy-monogramme', 'luxe', 115000, 'sac-09.png', 'Monogramme brun', '#78350f', true],
            ['Sac Crossbody Monogramme Noir', 'sac-crossbody-noir', 'bandouliere', 98000, 'sac-10.png', 'Noir', '#1a1a1a', false],
        ];
    }

    public static function imagePath(string $filename): string
    {
        return '/images/products/'.$filename;
    }
}
