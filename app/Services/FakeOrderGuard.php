<?php

namespace App\Services;

use App\Models\BlockedPhone;
use App\Models\Order;

/**
 * Simple fake-order protection for Cash on Delivery shops.
 *
 *  1. Phone is on the admin block list           -> order refused
 *  2. Too many orders from one phone in 24 hours  -> order refused
 *  3. Phone has several cancelled orders lately   -> Cash on Delivery refused,
 *     customer must pay first with bKash / Nagad / Online Payment
 *
 * check() returns a message to show the customer, or null when the order is fine.
 * It never throws: if something goes wrong the order is allowed.
 */
class FakeOrderGuard
{
    public function check(?string $phone, ?string $paymentMethod): ?string
    {
        if (! config("shop.fraud.enabled", true)) {
            return null;
        }

        try {
            $n = SmsService::normalizePhone($phone);

            if ($n === null) {
                return null;
            }

            if (BlockedPhone::where("phone", $n)->exists()) {
                return "Sorry, we cannot accept an order from this phone number. Please contact us.";
            }

            $maxPerDay = (int) config("shop.fraud.max_orders_per_day", 5);

            if ($maxPerDay > 0 && $this->ordersFor($n)
                    ->where("created_at", ">=", now()->subDay())
                    ->whereRaw("LOWER(status) <> ?", ["cancelled"])
                    ->count() >= $maxPerDay) {
                return "Too many orders were placed from this number today. Please contact us to continue.";
            }

            $maxCancelled = (int) config("shop.fraud.max_cancelled", 3);

            if ($maxCancelled > 0
                && $paymentMethod === "Cash on Delivery"
                && $this->ordersFor($n)
                    ->where("created_at", ">=", now()->subDays(90))
                    ->whereRaw("LOWER(status) = ?", ["cancelled"])
                    ->count() >= $maxCancelled) {
                return "Cash on Delivery is not available for this number. Please pay with bKash, Nagad or Online Payment.";
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    /** Orders whose stored phone ends with the same 10 digits (ignores dashes, spaces, +88). */
    protected function ordersFor(string $normalized)
    {
        $tail = substr($normalized, -10);

        return Order::whereRaw(
            "REPLACE(REPLACE(REPLACE(phone, \x27-\x27, \x27\x27), \x27 \x27, \x27\x27), \x27+\x27, \x27\x27) LIKE ?",
            ["%" . $tail]
        );
    }
}
