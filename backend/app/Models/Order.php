<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_city',
        'shipping_address',
        'notes',
        'status',
        'subtotal',
        'shipping_fee',
        'total',
        'currency',
        'confirmed_at',
        'shipped_at',
        'arrived_gabon_at',
        'delivered_at',
        'estimated_delivery_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'arrived_gabon_at' => 'datetime',
            'delivered_at' => 'datetime',
            'estimated_delivery_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
