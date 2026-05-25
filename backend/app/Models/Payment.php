<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount_due',
        'amount_collected',
        'payment_method',
        'status',
        'proof_image',
        'admin_notes',
        'validated_by',
        'collected_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'collected_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
