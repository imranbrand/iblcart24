<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Steadfast Courier (portal.packzy.com) integration.
 * Disabled until STEADFAST_ENABLED=true and both keys are present in .env.
 */
class SteadfastService
{
    public function enabled(): bool
    {
        return (bool) config('logistics.steadfast.enabled')
            && (string) config('logistics.steadfast.api_key') !== ''
            && (string) config('logistics.steadfast.secret_key') !== '';
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('logistics.steadfast.base_url'), '/'))
            ->timeout((int) config('logistics.steadfast.timeout', 20))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Api-Key'    => (string) config('logistics.steadfast.api_key'),
                'Secret-Key' => (string) config('logistics.steadfast.secret_key'),
            ]);
    }

    /**
     * Create a parcel for the order.
     *
     * @return array{ok: bool, message: string, consignment_id?: string, tracking_code?: string, status?: string}
     */
    public function createParcel(Order $order): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'message' => 'Steadfast is not configured. Add the keys in .env first.'];
        }

        $phone = SmsService::normalizePhone($order->phone);

        if ($phone === null) {
            return ['ok' => false, 'message' => 'Customer phone number is not a valid Bangladeshi mobile number.'];
        }

        $order->loadMissing('items');

        // Steadfast wants the 11-digit local format (01XXXXXXXXX).
        $recipientPhone = substr($phone, 2);

        // Already paid online -> courier must collect nothing.
        $cod = strtolower((string) $order->payment_status) === 'paid'
            ? 0
            : (float) ($order->total ?: $order->total_amount);

        $items = $order->items
            ->map(fn ($item) => ($item->product_name ?? 'Item') . ' x' . (int) $item->quantity)
            ->implode(', ');

        $payload = [
            'invoice'           => 'IBL-' . $order->id,
            'recipient_name'    => Str::limit((string) $order->customer_name, 90, ''),
            'recipient_phone'   => $recipientPhone,
            'recipient_address' => Str::limit((string) $order->address, 240, ''),
            'cod_amount'        => $cod,
            'note'              => Str::limit((string) $order->note, 200, ''),
            'item_description'  => Str::limit($items, 200, ''),
        ];

        try {
            $response = $this->client()->post('/create_order', $payload);
            $json     = $response->json() ?? [];

            $consignment = $json['consignment'] ?? null;

            if ($response->successful() && is_array($consignment) && ! empty($consignment['consignment_id'])) {
                return [
                    'ok'             => true,
                    'message'        => (string) ($json['message'] ?? 'Parcel created.'),
                    'consignment_id' => (string) $consignment['consignment_id'],
                    'tracking_code'  => (string) ($consignment['tracking_code'] ?? ''),
                    'status'         => (string) ($consignment['status'] ?? 'in_review'),
                ];
            }

            Log::warning('[Steadfast] create_order rejected', [
                'order' => $order->id,
                'http'  => $response->status(),
                'body'  => mb_substr((string) $response->body(), 0, 400),
            ]);

            return ['ok' => false, 'message' => $this->errorMessage($json, $response->status())];
        } catch (Throwable $e) {
            Log::error('[Steadfast] create_order failed: ' . $e->getMessage());

            return ['ok' => false, 'message' => 'Could not reach Steadfast. Please try again in a minute.'];
        }
    }

    /**
     * Ask Steadfast for the current delivery status of a consignment.
     *
     * @return array{ok: bool, message: string, status?: string}
     */
    public function deliveryStatus(string $consignmentId): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'message' => 'Steadfast is not configured.'];
        }

        try {
            $response = $this->client()->get('/status_by_cid/' . urlencode($consignmentId));
            $json     = $response->json() ?? [];

            if ($response->successful() && ! empty($json['delivery_status'])) {
                return [
                    'ok'      => true,
                    'message' => 'Status refreshed.',
                    'status'  => (string) $json['delivery_status'],
                ];
            }

            return ['ok' => false, 'message' => $this->errorMessage($json, $response->status())];
        } catch (Throwable $e) {
            Log::error('[Steadfast] status check failed: ' . $e->getMessage());

            return ['ok' => false, 'message' => 'Could not reach Steadfast. Please try again in a minute.'];
        }
    }

    protected function errorMessage(array $json, int $http): string
    {
        if (! empty($json['message']) && is_string($json['message'])) {
            return $json['message'];
        }

        if (! empty($json['errors']) && is_array($json['errors'])) {
            $first = collect($json['errors'])->flatten()->first();

            if (is_string($first)) {
                return $first;
            }
        }

        return 'Steadfast returned an error (HTTP ' . $http . ').';
    }
}
