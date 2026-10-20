<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    /**
     * Public order tracking form (no login needed).
     */
    public function index()
    {
        return view('track.index', ['order' => null]);
    }

    /**
     * Look an order up by order number + the phone number used at checkout.
     * Both must match, so order numbers cannot be guessed to view other people's orders.
     */
    public function lookup(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'min:1'],
            'phone'    => ['required', 'string', 'max:30'],
        ]);

        $order = Order::with('items')->find($data['order_id']);

        $given  = $this->digits($data['phone']);
        $stored = $order ? $this->digits((string) $order->phone) : '';

        // Compare the last 10 digits so +880 / 0 prefixes do not matter.
        $matches = $order
            && $given !== ''
            && substr($given, -10) === substr($stored, -10);

        if (! $matches) {
            return back()
                ->withInput()
                ->withErrors(['order_id' => 'No order found with that order number and phone number.']);
        }

        return view('track.index', ['order' => $order]);
    }

    private function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }
}