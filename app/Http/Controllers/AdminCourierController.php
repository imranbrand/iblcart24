<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\SmsService;
use App\Services\SteadfastService;

class AdminCourierController extends Controller
{
    /**
     * Send an order to Steadfast and store the consignment details.
     */
    public function send(Order $order, SteadfastService $steadfast, SmsService $sms)
    {
        $back = redirect()->route('admin.orders.show', $order);

        if ($order->isCancelled()) {
            return $back->with('error', 'A cancelled order cannot be sent to a courier.');
        }

        if (! empty($order->courier_consignment_id)) {
            return $back->with('error', 'This order was already sent to ' . ($order->courier_name ?: 'a courier') . '.');
        }

        if (! $steadfast->enabled()) {
            return $back->with('error', 'Steadfast is not enabled. Add STEADFAST_ENABLED=true and your keys to .env.');
        }

        $result = $steadfast->createParcel($order);

        if (! $result['ok']) {
            return $back->with('error', 'Steadfast: ' . $result['message']);
        }

        $order->courier_name           = 'Steadfast';
        $order->courier_consignment_id = $result['consignment_id'];
        $order->courier_tracking_code  = $result['tracking_code'] ?? null;
        $order->courier_status         = $result['status'] ?? 'in_review';
        $order->courier_sent_at        = now();

        // A parcel handed to the courier means the order is on its way.
        $movedToShipped = in_array(strtolower((string) $order->status), ['pending', 'confirmed', 'processing'], true);

        if ($movedToShipped) {
            $order->status = 'shipped';
        }

        $order->save();

        if ($movedToShipped) {
            try {
                $sms->notifyStatusChange($order);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $back->with('success', 'Sent to Steadfast. Tracking code: ' . ($order->courier_tracking_code ?: $order->courier_consignment_id));
    }

    /**
     * Pull the latest delivery status from Steadfast.
     */
    public function refresh(Order $order, SteadfastService $steadfast)
    {
        $back = redirect()->route('admin.orders.show', $order);

        if (empty($order->courier_consignment_id)) {
            return $back->with('error', 'This order has not been sent to a courier yet.');
        }

        $result = $steadfast->deliveryStatus((string) $order->courier_consignment_id);

        if (! $result['ok']) {
            return $back->with('error', 'Steadfast: ' . $result['message']);
        }

        $order->courier_status = $result['status'];
        $order->save();

        return $back->with('success', 'Courier status: ' . str_replace('_', ' ', $result['status']));
    }
}
