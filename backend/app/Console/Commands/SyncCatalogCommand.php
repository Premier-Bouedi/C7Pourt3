<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\CatalogData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncCatalogCommand extends Command
{
    protected $signature = 'catalog:sync {--images : Copier les images depuis le dossier images/ du projet}';

    protected $description = 'Synchronise les 10 produits C7Pourt3 (données, stock, images)';

    public function handle(): int
    {
        if ($this->option('images')) {
            $this->syncImages();
        }

        $slugs = [];

        foreach (CatalogData::products() as [$name, $slug, $cat, $price, $img, $color, $hex, $featured]) {
            $slugs[] = $slug;
            $path = CatalogData::imagePath($img);

            $product = Product::withTrashed()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => 'Sac premium C7Pourt3. Stock Maroc, livraison Gabon ~8 jours, paiement à la livraison (COD).',
                    'base_price' => $price,
                    'images' => [$path],
                    'stock_morocco' => CatalogData::STOCK_MOROCCO,
                    'is_active' => true,
                    'is_featured' => $featured,
                    'category' => $cat,
                    'deleted_at' => null,
                ],
            );

            ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'sku' => strtoupper($slug)],
                [
                    'color' => $color,
                    'color_hex' => $hex,
                    'price' => $price,
                    'stock' => CatalogData::STOCK_VARIANT,
                    'image' => $path,
                    'is_active' => true,
                ],
            );

            $this->line("✓ {$name}");
        }

        Product::whereNotIn('slug', $slugs)->each(function (Product $p) {
            $p->variants()->delete();
            $p->forceDelete();
            $this->warn("Supprimé (hors catalogue) : {$p->name}");
        });

        $this->newLine();
        $this->info('Catalogue synchronisé : '.Product::count().' produits actifs.');

        return self::SUCCESS;
    }

    private function syncImages(): void
    {
        $src = base_path('../images');
        $dest = public_path('images/products');

        if (! File::isDirectory($src)) {
            $this->warn("Dossier source introuvable : {$src}");

            return;
        }

        File::ensureDirectoryExists($dest);

        foreach (range(1, 10) as $i) {
            $from = $src.DIRECTORY_SEPARATOR.'SAC ('.$i.').png';
            $to = $dest.DIRECTORY_SEPARATOR.sprintf('sac-%02d.png', $i);

            if (File::exists($from)) {
                File::copy($from, $to);
                $this->line("Image : sac-".sprintf('%02d', $i).'.png');
            }
        }
    }
}
