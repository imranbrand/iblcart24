<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'phone',
        'address',
        'total_amount',
        'subtotal',
        'delivery_charge',
        'total',
        'payment_method',
        'payment_transaction_id',
        'payment_gateway',
        'payment_status',
        'paid_at',
        'status',
        'note',
        'coupon_code',
        'discount_amount',
        'courier_name',
        'courier_consignment_id',
        'courier_tracking_code',
        'courier_status',
        'courier_sent_at',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'total'           => 'decimal:2',
        'paid_at'         => 'datetime',
        'courier_sent_at' => 'datetime',
    ];

    /**
     * Customer/User who placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function isCancelled(): bool
    {
        return strtolower((string) $this->status) === 'cancelled';
    }

    public function isDelivered(): bool
    {
        return in_array(
            strtolower((string) $this->status),
            ['delivered', 'completed'],
            true
        );
    }

    public function isPending(): bool
    {
        return strtolower((string) $this->status) === 'pending';
    }

    public function isConfirmed(): bool
    {
        return strtolower((string) $this->status) === 'confirmed';
    }

    public function isProcessing(): bool
    {
        return strtolower((string) $this->status) === 'processing';
    }

    public function isShipped(): bool
    {
        return strtolower((string) $this->status) === 'shipped';
    }

    public function isPaid(): bool
    {
        return strtolower((string) $this->payment_status) === 'paid';
    }

    public function isPaymentPending(): bool
    {
        return strtolower((string) $this->payment_status) === 'pending';
    }

    public function isPaymentFailed(): bool
    {
        return strtolower((string) $this->payment_status) === 'failed';
    }

    public function isRefunded(): bool
    {
        return strtolower((string) $this->payment_status) === 'refunded';
    }

    /**
     * Get final order amount.
     */
    public function getFinalAmountAttribute()
    {
        if ($this->total_amount !== null) {
            return $this->total_amount;
        }

        return $this->total ?? 0;
    }

    /**
     * Active/non-cancelled orders.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('status')
                ->orWhereNotIn('status', ['cancelled']);
        });
    }

    /**
     * Cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Paid orders.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    /**
     * Pending payment orders.
     */
    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', 'Pending');
    }
}