<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'sku', 'color', 'color_hex', 'price', 'stock', 'image', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function effectivePrice(): int
    {
        return $this->price ?? $this->product->base_price;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0 && $this->is_active;
    }

    public function imageForUpload(): ?string
    {
        return $this->image ? basename($this->image) : null;
    }
}
