<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'transaction_code',
    'customer_id',
    'sold_by',
    'subtotal',
    'discount_amount',
    'total_amount',
    'payment_method',
    'gcash_reference',
    'cash_amount',
    'gcash_amount',
    'order_type',
    'amount_received',
    'change_amount',
    'status',
    'void_reason',
    'notes',
    'completed_at',
])]
class Sale extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by')->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'cash_amount' => 'decimal:2',
            'gcash_amount' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }
}
