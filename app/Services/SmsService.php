<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sends SMS to Bangladeshi numbers.
 *
 * Driver "log"        : only writes to the Laravel log (default, safe for testing).
 * Driver "bulksmsbd"  : real SMS through bulksmsbd.net (needs SMS_API_KEY + SMS_SENDER_ID).
 *
 * Every public method is best-effort: it never throws, so an SMS problem can
 * never break checkout or the admin panel.
 */
class SmsService
{
    /**
     * Convert 01XXXXXXXXX / +8801XXXXXXXXX / 8801XXXXXXXXX into 8801XXXXXXXXX.
     * Returns null when the number is not a valid BD mobile number.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '' || $digits === null) {
            return null;
        }

        if (strlen($digits) === 13 && str_starts_with($digits, '8801')) {
            return $digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            return '88' . $digits;
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '880' . $digits;
        }

        return null;
    }

    /**
     * Send one SMS. Returns true when it was accepted.
     */
    public function send(?string $phone, string $message): bool
    {
        $number = self::normalizePhone($phone);

        if ($number === null) {
            Log::warning('[SMS] skipped: invalid phone number', ['phone' => $phone]);

            return false;
        }

        $driver = strtolower((string) config('logistics.sms.driver', 'log'));

        try {
            if ($driver === 'bulksmsbd') {
                return $this->sendViaBulkSmsBd($number, $message);
            }

            Log::info('[SMS:log] to ' . $number . ': ' . $message);

            return true;
        } catch (Throwable $e) {
            Log::error('[SMS] send failed: ' . $e->getMessage());

            return false;
        }
    }

    protected function sendViaBulkSmsBd(string $number, string $message): bool
    {
        $apiKey   = (string) config('logistics.sms.api_key');
        $senderId = (string) config('logistics.sms.sender_id');
        $url      = (string) config('logistics.sms.url');

        if ($apiKey === '' || $senderId === '') {
            Log::warning('[SMS] bulksmsbd selected but SMS_API_KEY / SMS_SENDER_ID is empty.');

            return false;
        }

        $response = Http::timeout(10)->get($url, [
            'api_key'  => $apiKey,
            'type'     => 'text',
            'number'   => $number,
            'senderid' => $senderId,
            'message'  => $message,
        ]);

        $code = $response->json('response_code');

        if ($response->successful() && (int) $code === 202) {
            return true;
        }

        Log::warning('[SMS] bulksmsbd rejected the message', [
            'http'   => $response->status(),
            'body'   => mb_substr((string) $response->body(), 0, 300),
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Order notifications
    |--------------------------------------------------------------------------
    */

    public function notifyOrderPlaced(Order $order): void
    {
        if (! config('logistics.sms.on_order_placed')) {
            return;
        }

        $message = sprintf(
            '%s: Order #%d received. Total Tk %s. Track: %s',
            config('shop.name', config('app.name')),
            $order->id,
            number_format((float) ($order->total ?: $order->total_amount), 0),
            url('/track-order')
        );

        $this->send($order->phone, $message);
    }

    public function notifyStatusChange(Order $order): void
    {
        if (! config('logistics.sms.on_status_change')) {
            return;
        }

        $shop   = config('shop.name', config('app.name'));
        $status = strtolower((string) $order->status);

        $message = match ($status) {
            'confirmed' => sprintf('%s: Order #%d is confirmed. We will ship it soon.', $shop, $order->id),
            'shipped'   => $this->shippedMessage($order, $shop),
            'delivered' => sprintf('%s: Order #%d has been delivered. Thank you for shopping with us!', $shop, $order->id),
            'cancelled' => sprintf('%s: Order #%d has been cancelled. Call us if you have any question.', $shop, $order->id),
            default     => null,
        };

        if ($message !== null) {
            $this->send($order->phone, $message);
        }
    }

    protected function shippedMessage(Order $order, string $shop): string
    {
        $message = sprintf('%s: Order #%d has been shipped.', $shop, $order->id);

        if (! empty($order->courier_tracking_code)) {
            $message .= ' Courier tracking code: ' . $order->courier_tracking_code . '.';
        }

        return $message;
    }
}
