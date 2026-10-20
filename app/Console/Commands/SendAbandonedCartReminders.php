<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Models\BlockedPhone;
use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Console\Command;

class SendAbandonedCartReminders extends Command
{
    protected $signature   = 'shop:abandoned-reminders';
    protected $description = 'Send ONE reminder SMS to customers who left items in the cart at checkout';

    public function handle(SmsService $sms): int
    {
        if (! config('shop.abandoned_cart.enabled', true)) {
            $this->info('Abandoned cart reminders are switched off.');

            return self::SUCCESS;
        }

        $delay  = (int) config('shop.abandoned_cart.delay_minutes', 60);
        $maxAge = (int) config('shop.abandoned_cart.max_age_hours', 72);
        $sent   = 0;

        $carts = AbandonedCart::whereNull('reminded_at')
            ->whereNull('recovered_at')
            ->where('last_seen_at', '<=', now()->subMinutes($delay))
            ->where('last_seen_at', '>=', now()->subHours($maxAge))
            ->orderBy('id')
            ->limit(50)
            ->get();

        foreach ($carts as $cart) {
            if (BlockedPhone::where('phone', $cart->phone)->exists()) {
                continue;
            }

            // Customer already ordered after leaving the cart? Then do not nag.
            $tail    = substr($cart->phone, -10);
            $ordered = Order::whereRaw(
                "REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '+', '') LIKE ?",
                ['%' . $tail]
            )->where('created_at', '>=', $cart->created_at)->exists();

            if ($ordered) {
                $cart->update(['recovered_at' => now()]);

                continue;
            }

            $message = sprintf(
                '%s: You left items in your cart. Complete your order here: %s',
                config('app.name'),
                route('cart.recover', $cart->token)
            );

            if ($sms->send($cart->phone, $message)) {
                $cart->update(['reminded_at' => now()]);
                $sent++;
            }
        }

        $this->info("Reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
