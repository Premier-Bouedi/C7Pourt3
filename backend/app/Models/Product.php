<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'base_price', 'compare_at_price',
        'images', 'stock_morocco', 'is_active', 'is_featured', 'category',
        'average_rating', 'reviews_count',
    ];

    protected function casts(): array
    {
        return ['images' => 'array', 'is_active' => 'boolean', 'is_featured' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function displayPrice(): int
    {
        $v = $this->variants()->where('is_active', true)->orderBy('price')->first();

        return $v?->price ?? $this->base_price;
    }

    /** Fichiers pour FileUpload Filament (noms dans public/images/products). */
    public function imagesForUpload(): array
    {
        return collect($this->images ?? [])
            ->map(function (string $path) {
                if (str_starts_with($path, '/images/products/')) {
                    return basename($path);
                }
                if (str_starts_with($path, 'images/products/')) {
                    return basename($path);
                }

                return basename($path);
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function pathsFromUpload(?array $files): array
    {
        return collect($files ?? [])
            ->filter()
            ->map(fn ($f) => '/images/products/'.basename((string) $f))
            ->values()
            ->all();
    }

    public function primaryImageUrl(): ?string
    {
        $path = $this->images[0] ?? null;
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : url($path);
    }
}
