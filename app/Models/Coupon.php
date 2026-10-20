<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Find an active, usable coupon by its code.
     */
    public static function findValidByCode(string $code): ?self
    {
        $coupon = static::query()
            ->whereRaw('UPPER(TRIM(code)) = ?', [strtoupper(trim($code))])
            ->first();

        if (!$coupon || !$coupon->isCurrentlyValid()) {
            return null;
        }

        return $coupon;
    }

    /**
     * Whether the coupon can be used right now (ignoring order total).
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     * Returns 0 if the coupon does not apply (e.g. below min order amount).
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'fixed') {
            $discount = (float) $this->value;
        } else {
            $discount = $subtotal * ((float) $this->value / 100);

            if ($this->max_discount_amount !== null) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
        }

        // Never discount more than the subtotal itself.
        return round(min($discount, $subtotal), 2);
    }
}