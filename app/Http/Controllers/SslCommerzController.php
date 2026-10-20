<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SslCommerzController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INITIATE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function initiate(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Customer ownership
        |--------------------------------------------------------------------------
        */

        if (
            !$order->user_id ||
            (int) $order->user_id !== (int) auth()->id()
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Payment method check
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(trim((string) $order->payment_method))
            !== strtolower('Online Payment')
        ) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'This order is not an online payment order.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Already paid
        |--------------------------------------------------------------------------
        */

        if ($this->isPaid($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'success',
                    'This order has already been paid.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cancelled orders cannot start a new payment
        |--------------------------------------------------------------------------
        */

        if ($this->isCancelled($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'This order has been cancelled and cannot be paid.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SSLCommerz configuration
        |--------------------------------------------------------------------------
        */

        $storeId = trim(
            (string) config('services.sslcommerz.store_id')
        );

        $storePassword = trim(
            (string) config('services.sslcommerz.store_password')
        );

        $initUrl = trim(
            (string) config('services.sslcommerz.init_url')
        );

        $successUrl = trim(
            (string) config('services.sslcommerz.success_url')
        );

        $failUrl = trim(
            (string) config('services.sslcommerz.fail_url')
        );

        $cancelUrl = trim(
            (string) config('services.sslcommerz.cancel_url')
        );

        $ipnUrl = trim(
            (string) config('services.sslcommerz.ipn_url')
        );

        if (
            $storeId === '' ||
            $storePassword === '' ||
            $initUrl === '' ||
            $successUrl === '' ||
            $failUrl === '' ||
            $cancelUrl === ''
        ) {
            Log::error(
                'SSLCommerz configuration missing',
                [
                    'order_id' => $order->id,
                    'missing' => [
                        'store_id' => $storeId === '',
                        'store_password' => $storePassword === '',
                        'init_url' => $initUrl === '',
                        'success_url' => $successUrl === '',
                        'fail_url' => $failUrl === '',
                        'cancel_url' => $cancelUrl === '',
                    ],
                ]
            );

            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'SSLCommerz payment is not configured correctly.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate order amount BEFORE creating transaction ID
        |--------------------------------------------------------------------------
        */

        $totalAmount = $this->getOrderAmount($order);

        if ($totalAmount <= 0) {
            Log::error(
                'Invalid SSLCommerz order amount',
                [
                    'order_id' => $order->id,
                    'amount' => $totalAmount,
                ]
            );

            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Invalid order amount.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Make sure order has items
        |--------------------------------------------------------------------------
        */

        $numberOfItems = max(
            1,
            (int) $order->items()->sum('quantity')
        );

        /*
        |--------------------------------------------------------------------------
        | Generate unique merchant transaction ID
        |--------------------------------------------------------------------------
        |
        | Keep it short enough for gateway compatibility.
        |
        */

        try {
            $tranId = 'IBL-' .
                $order->id .
                '-' .
                strtoupper(
                    bin2hex(random_bytes(5))
                );
        } catch (Throwable $e) {
            Log::error(
                'Unable to generate SSLCommerz transaction ID',
                [
                    'order_id' => $order->id,
                    'message' => $e->getMessage(),
                ]
            );

            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Unable to start online payment.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Save payment attempt
        |--------------------------------------------------------------------------
        */

        $order->update([
            'payment_transaction_id' => $tranId,
            'payment_gateway' => 'SSLCommerz',
            'payment_status' => 'Pending',
            'paid_at' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        */

        $user = $order->user;

        $customerEmail = trim(
            (string) ($user?->email ?? '')
        );

        if ($customerEmail === '') {
            $customerEmail = 'customer@example.com';
        }

        $customerName = trim(
            (string) ($order->customer_name ?? 'Customer')
        );

        $customerPhone = trim(
            (string) ($order->phone ?? '01700000000')
        );

        $customerAddress = trim(
            (string) ($order->address ?? 'Dhaka')
        );

        /*
        |--------------------------------------------------------------------------
        | SSLCommerz request
        |--------------------------------------------------------------------------
        */

        $postData = [
            'store_id' => $storeId,
            'store_passwd' => $storePassword,

            'total_amount' => number_format(
                $totalAmount,
                2,
                '.',
                ''
            ),

            'currency' => 'BDT',

            'tran_id' => $tranId,

            /*
            |--------------------------------------------------------------------------
            | Callback URLs
            |--------------------------------------------------------------------------
            */

            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'cancel_url' => $cancelUrl,

            'ipn_url' => $ipnUrl,

            /*
            |--------------------------------------------------------------------------
            | Customer information
            |--------------------------------------------------------------------------
            */

            'cus_name' => $this->limitLength(
                $customerName,
                50
            ),

            'cus_email' => $this->limitLength(
                $customerEmail,
                50
            ),

            'cus_add1' => $this->limitLength(
                $customerAddress,
                50
            ),

            'cus_add2' => '',

            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',

            'cus_phone' => $this->limitLength(
                $customerPhone,
                20
            ),

            'cus_fax' => '',

            /*
            |--------------------------------------------------------------------------
            | Shipping information
            |--------------------------------------------------------------------------
            */

            'shipping_method' => 'Courier',

            'ship_name' => $this->limitLength(
                $customerName,
                50
            ),

            'ship_add1' => $this->limitLength(
                $customerAddress,
                50
            ),

            'ship_add2' => '',

            'ship_city' => 'Dhaka',
            'ship_state' => 'Dhaka',
            'ship_postcode' => '1000',
            'ship_country' => 'Bangladesh',

            /*
            |--------------------------------------------------------------------------
            | Product information
            |--------------------------------------------------------------------------
            */

            'product_name' => $this->limitLength(
                'IBL CART 24 Order #' . $order->id,
                100
            ),

            'product_category' => 'jewellery',

            'product_profile' => 'physical-goods',

            'num_of_item' => $numberOfItems,

            /*
            |--------------------------------------------------------------------------
            | Custom values
            |--------------------------------------------------------------------------
            */

            'value_a' => (string) $order->id,
            'value_b' => (string) $order->user_id,
            'value_c' => 'IBL CART 24',
            'value_d' => '',
        ];

        try {
            /*
            |--------------------------------------------------------------------------
            | Send request to SSLCommerz
            |--------------------------------------------------------------------------
            */

            $response = Http::asForm()
                ->acceptJson()
                ->connectTimeout(10)
                ->timeout(30)
                ->post(
                    $initUrl,
                    $postData
                );

            /*
            |--------------------------------------------------------------------------
            | HTTP failure
            |--------------------------------------------------------------------------
            */

            if (!$response->successful()) {
                Log::error(
                    'SSLCommerz initiation HTTP error',
                    [
                        'order_id' => $order->id,
                        'tran_id' => $tranId,
                        'http_status' => $response->status(),
                    ]
                );

                $this->cancelAndRestoreStock(
                    $order,
                    'SSLCommerz connection failed.'
                );

                return redirect()
                    ->route('orders.show', $order)
                    ->with(
                        'error',
                        'Unable to connect to SSLCommerz.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Parse response
            |--------------------------------------------------------------------------
            */

            $data = $response->json();

            if (!is_array($data)) {
                Log::error(
                    'SSLCommerz returned invalid JSON',
                    [
                        'order_id' => $order->id,
                        'tran_id' => $tranId,
                    ]
                );

                $this->cancelAndRestoreStock(
                    $order,
                    'Invalid SSLCommerz response.'
                );

                return redirect()
                    ->route('orders.show', $order)
                    ->with(
                        'error',
                        'Invalid response received from SSLCommerz.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Check gateway response
            |--------------------------------------------------------------------------
            */

            $gatewayStatus = strtoupper(
                trim(
                    (string) ($data['status'] ?? '')
                )
            );

            $gatewayPageUrl = trim(
                (string) ($data['GatewayPageURL'] ?? '')
            );

            if (
                $gatewayStatus !== 'SUCCESS' ||
                $gatewayPageUrl === ''
            ) {
                Log::error(
                    'SSLCommerz initiation failed',
                    [
                        'order_id' => $order->id,
                        'tran_id' => $tranId,
                        'gateway_status' => $gatewayStatus,
                        'failed_reason' => $data['failedreason'] ?? null,
                    ]
                );

                $this->cancelAndRestoreStock(
                    $order,
                    'SSLCommerz payment initiation failed.'
                );

                return redirect()
                    ->route('orders.show', $order)
                    ->with(
                        'error',
                        $data['failedreason']
                        ?? 'SSLCommerz payment initiation failed.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Log gateway session
            |--------------------------------------------------------------------------
            |
            | Do NOT log store password.
            |
            */

            if (!empty($data['sessionkey'])) {
                Log::info(
                    'SSLCommerz payment session created',
                    [
                        'order_id' => $order->id,
                        'tran_id' => $tranId,
                        'session_created' => true,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Redirect customer to SSLCommerz
            |--------------------------------------------------------------------------
            */

            return redirect()->away(
                $gatewayPageUrl
            );
        } catch (Throwable $e) {
            Log::error(
                'SSLCommerz initiation exception',
                [
                    'order_id' => $order->id,
                    'tran_id' => $tranId,
                    'message' => $e->getMessage(),
                ]
            );

            $this->cancelAndRestoreStock(
                $order,
                'Payment gateway connection failed.'
            );

            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Payment gateway connection failed.'
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SUCCESS CALLBACK
    |--------------------------------------------------------------------------
    */

    public function success(Request $request)
    {
        return $this->handleCallback($request);
    }


    /*
    |--------------------------------------------------------------------------
    | FAIL CALLBACK
    |--------------------------------------------------------------------------
    */

    public function fail(Request $request)
    {
        $tranId = $this->getTransactionId($request);

        if ($tranId === '') {
            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Online payment failed.'
                );
        }

        $order = $this->findOrderByTransactionId(
            $tranId
        );

        if (!$order) {
            Log::warning(
                'SSLCommerz fail callback order not found',
                [
                    'tran_id' => $tranId,
                ]
            );

            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Online payment failed. Order not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | If payment was already verified by IPN, do NOT cancel it.
        |--------------------------------------------------------------------------
        */

        if ($this->isPaid($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'success',
                    'Payment has already been verified.'
                );
        }

        $this->cancelAndRestoreStock(
            $order,
            'Online payment failed.'
        );

        return redirect()
            ->route('orders.show', $order)
            ->with(
                'error',
                'Online payment failed. Your reserved stock has been restored.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL CALLBACK
    |--------------------------------------------------------------------------
    */

    public function cancel(Request $request)
    {
        $tranId = $this->getTransactionId($request);

        if ($tranId === '') {
            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Online payment was cancelled.'
                );
        }

        $order = $this->findOrderByTransactionId(
            $tranId
        );

        if (!$order) {
            Log::warning(
                'SSLCommerz cancel callback order not found',
                [
                    'tran_id' => $tranId,
                ]
            );

            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Online payment was cancelled. Order not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | If IPN already marked it paid, don't cancel.
        |--------------------------------------------------------------------------
        */

        if ($this->isPaid($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'success',
                    'Payment has already been verified.'
                );
        }

        $this->cancelAndRestoreStock(
            $order,
            'Online payment was cancelled.'
        );

        return redirect()
            ->route('orders.show', $order)
            ->with(
                'error',
                'Online payment was cancelled. Your reserved stock has been restored.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | IPN
    |--------------------------------------------------------------------------
    */

    public function ipn(Request $request)
    {
        Log::info(
            'SSLCommerz IPN received',
            [
                'tran_id' => $request->input('tran_id'),
                'val_id_present' => $request->filled('val_id'),
                'status' => $request->input('status'),
            ]
        );

        $tranId = $this->getTransactionId($request);

        if ($tranId === '') {
            return response(
                'INVALID',
                400
            );
        }

        $order = $this->findOrderByTransactionId(
            $tranId
        );

        if (!$order) {
            return response(
                'ORDER_NOT_FOUND',
                404
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Already paid
        |--------------------------------------------------------------------------
        */

        if ($this->isPaid($order)) {
            return response(
                'VALID',
                200
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate transaction with SSLCommerz
        |--------------------------------------------------------------------------
        */

        $validation = $this->validateTransaction(
            $tranId
        );

        if (!$validation['valid']) {
            Log::warning(
                'SSLCommerz IPN validation failed',
                [
                    'order_id' => $order->id,
                    'tran_id' => $tranId,
                    'reason' => $validation['reason'] ?? null,
                ]
            );

            return response(
                'VALIDATION_FAILED',
                400
            );
        }

        $data = $validation['data'];

        /*
        |--------------------------------------------------------------------------
        | Transaction ID verification
        |--------------------------------------------------------------------------
        */

        if (
            !isset($data['tran_id']) ||
            (string) $data['tran_id'] !== $tranId
        ) {
            Log::warning(
                'SSLCommerz IPN transaction mismatch',
                [
                    'order_id' => $order->id,
                    'expected' => $tranId,
                    'received' => $data['tran_id'] ?? null,
                ]
            );

            return response(
                'TRANSACTION_MISMATCH',
                400
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Amount verification
        |--------------------------------------------------------------------------
        */

        if (!$this->amountMatchesOrder($order, $data)) {
            return response(
                'AMOUNT_MISMATCH',
                400
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Currency verification
        |--------------------------------------------------------------------------
        */

        if (!$this->currencyIsValid($data)) {
            return response(
                'CURRENCY_MISMATCH',
                400
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Mark paid
        |--------------------------------------------------------------------------
        */

        $this->markOrderPaid(
            $order,
            $data
        );

        return response(
            'VALID',
            200
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SUCCESS CALLBACK HANDLER
    |--------------------------------------------------------------------------
    */

    private function handleCallback(Request $request)
    {
        $tranId = $this->getTransactionId($request);

        if ($tranId === '') {
            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Invalid payment response.'
                );
        }

        $order = $this->findOrderByTransactionId(
            $tranId
        );

        if (!$order) {
            Log::warning(
                'SSLCommerz success callback order not found',
                [
                    'tran_id' => $tranId,
                ]
            );

            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Order not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Security:
        | The browser returns from SSLCommerz as a cross-site POST, so the
        | Laravel session cookie (SameSite=Lax) is NOT sent and auth()->id()
        | is null here. Ownership therefore cannot be checked by session.
        | Trust comes from the server-to-server validation below
        | (validation API + tran_id + amount + currency), not from the browser.
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Already paid
        |--------------------------------------------------------------------------
        */

        if ($this->isPaid($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'success',
                    'Payment has already been verified.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cancelled order cannot become paid
        |--------------------------------------------------------------------------
        */

        if ($this->isCancelled($order)) {
            Log::warning(
                'SSLCommerz success callback received for cancelled order',
                [
                    'order_id' => $order->id,
                    'tran_id' => $tranId,
                ]
            );

            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'This order has already been cancelled.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate transaction
        |--------------------------------------------------------------------------
        */

        $validation = $this->validateTransaction(
            $tranId
        );

        if (!$validation['valid']) {
            Log::warning(
                'SSLCommerz success validation failed',
                [
                    'order_id' => $order->id,
                    'tran_id' => $tranId,
                    'reason' => $validation['reason'] ?? null,
                ]
            );

            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Payment could not be verified.'
                );
        }

        $data = $validation['data'];

        /*
        |--------------------------------------------------------------------------
        | Transaction verification
        |--------------------------------------------------------------------------
        */

        if (
            !isset($data['tran_id']) ||
            (string) $data['tran_id'] !== $tranId
        ) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Transaction verification failed.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Amount verification
        |--------------------------------------------------------------------------
        */

        if (!$this->amountMatchesOrder($order, $data)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Payment amount verification failed.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Currency verification
        |--------------------------------------------------------------------------
        */

        if (!$this->currencyIsValid($data)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Payment currency verification failed.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Mark paid
        |--------------------------------------------------------------------------
        */

        $this->markOrderPaid(
            $order,
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | Reload order to determine actual final state
        |--------------------------------------------------------------------------
        */

        $order->refresh();

        if (!$this->isPaid($order)) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Payment could not be completed for this order.'
                );
        }

        return redirect()
            ->route('orders.show', $order)
            ->with(
                'success',
                'Payment completed successfully!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | MARK ORDER PAID
    |--------------------------------------------------------------------------
    */

    private function markOrderPaid(
        Order $order,
        array $data
    ): void {
        DB::transaction(
            function () use (
                $order,
                $data
            ) {
                $lockedOrder = Order::where(
                    'id',
                    $order->id
                )
                    ->lockForUpdate()
                    ->first();

                if (!$lockedOrder) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Idempotency
                |--------------------------------------------------------------------------
                */

                if ($this->isPaid($lockedOrder)) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Never mark cancelled order as paid
                |--------------------------------------------------------------------------
                */

                if ($this->isCancelled($lockedOrder)) {
                    Log::warning(
                        'Attempt to mark cancelled order as paid',
                        [
                            'order_id' => $lockedOrder->id,
                            'tran_id' => $lockedOrder->payment_transaction_id,
                        ]
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Verify transaction ID again
                |--------------------------------------------------------------------------
                */

                $gatewayTranId = trim(
                    (string) ($data['tran_id'] ?? '')
                );

                $merchantTranId = trim(
                    (string) $lockedOrder->payment_transaction_id
                );

                if (
                    $gatewayTranId === '' ||
                    $merchantTranId === '' ||
                    $gatewayTranId !== $merchantTranId
                ) {
                    Log::warning(
                        'SSLCommerz transaction ID mismatch while marking paid',
                        [
                            'order_id' => $lockedOrder->id,
                            'merchant_tran_id' => $merchantTranId,
                            'gateway_tran_id' => $gatewayTranId,
                        ]
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Verify amount again
                |--------------------------------------------------------------------------
                */

                if (!$this->amountMatchesOrder(
                    $lockedOrder,
                    $data
                )) {
                    Log::warning(
                        'SSLCommerz amount mismatch while marking paid',
                        [
                            'order_id' => $lockedOrder->id,
                            'tran_id' => $merchantTranId,
                        ]
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Mark payment as paid
                |--------------------------------------------------------------------------
                */

                $lockedOrder->payment_status = 'Paid';

                if (!$lockedOrder->paid_at) {
                    $lockedOrder->paid_at = now();
                }

                $lockedOrder->payment_gateway = 'SSLCommerz';

                $lockedOrder->save();

                Log::info(
                    'SSLCommerz order marked as paid',
                    [
                        'order_id' => $lockedOrder->id,
                        'tran_id' => $merchantTranId,
                        'bank_tran_id' => $data['bank_tran_id'] ?? null,
                    ]
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL ORDER + RESTORE STOCK
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Stock is deducted when the order is created.
    |
    | Therefore failed/cancelled online payments restore the reserved
    | quantity exactly once.
    |
    | We use the order's cancelled state as the idempotency guard.
    |
    */

    private function cancelAndRestoreStock(
        Order $order,
        string $reason
    ): void {
        DB::transaction(
            function () use (
                $order,
                $reason
            ) {
                $lockedOrder = Order::where(
                    'id',
                    $order->id
                )
                    ->lockForUpdate()
                    ->first();

                if (!$lockedOrder) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | NEVER restore stock for a paid order
                |--------------------------------------------------------------------------
                */

                if ($this->isPaid($lockedOrder)) {
                    Log::warning(
                        'Stock restoration blocked for paid order',
                        [
                            'order_id' => $lockedOrder->id,
                            'reason' => $reason,
                        ]
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Already cancelled = already restored
                |--------------------------------------------------------------------------
                |
                | Do not increment stock again.
                |
                */

                if ($this->isCancelled($lockedOrder)) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Delivered orders must never restore stock
                |--------------------------------------------------------------------------
                */

                if (
                    strtolower(
                        trim(
                            (string) $lockedOrder->status
                        )
                    ) === 'delivered'
                ) {
                    Log::warning(
                        'Stock restoration blocked for delivered order',
                        [
                            'order_id' => $lockedOrder->id,
                            'reason' => $reason,
                        ]
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Load order items
                |--------------------------------------------------------------------------
                */

                $lockedOrder->load('items');

                /*
                |--------------------------------------------------------------------------
                | Restore stock
                |--------------------------------------------------------------------------
                */

                foreach ($lockedOrder->items as $item) {
                    if (!$item->product_id) {
                        continue;
                    }

                    $quantity = (int) $item->quantity;

                    if ($quantity <= 0) {
                        continue;
                    }

                    Product::where(
                        'id',
                        $item->product_id
                    )->increment(
                        'stock',
                        $quantity
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Mark order cancelled
                |--------------------------------------------------------------------------
                */

                $lockedOrder->status = 'cancelled';

                $lockedOrder->payment_status = 'Failed';

                $lockedOrder->paid_at = null;

                $lockedOrder->save();

                Log::info(
                    'SSLCommerz order cancelled and stock restored',
                    [
                        'order_id' => $lockedOrder->id,
                        'tran_id' => $lockedOrder->payment_transaction_id,
                        'reason' => $reason,
                    ]
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validateTransaction(
        string $tranId
    ): array {
        try {
            $queryUrl = trim(
                (string) config(
                    'services.sslcommerz.transaction_query_url'
                )
            );

            $storeId = trim(
                (string) config(
                    'services.sslcommerz.store_id'
                )
            );

            $storePassword = trim(
                (string) config(
                    'services.sslcommerz.store_password'
                )
            );

            if (
                $queryUrl === '' ||
                $storeId === '' ||
                $storePassword === ''
            ) {
                return [
                    'valid' => false,
                    'reason' =>
                        'SSLCommerz validation configuration is missing.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Transaction Query API
            |--------------------------------------------------------------------------
            */

            $response = Http::acceptJson()
                ->connectTimeout(10)
                ->timeout(30)
                ->get(
                    $queryUrl,
                    [
                        'tran_id' => $tranId,
                        'store_id' => $storeId,
                        'store_passwd' => $storePassword,
                        'format' => 'json',
                        'v' => 1,
                    ]
                );

            if (!$response->successful()) {
                return [
                    'valid' => false,
                    'reason' =>
                        'SSLCommerz validation HTTP error.',
                ];
            }

            $data = $response->json();

            if (!is_array($data)) {
                return [
                    'valid' => false,
                    'reason' =>
                        'Invalid response from SSLCommerz validation API.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | API connection
            |--------------------------------------------------------------------------
            */

            if (
                isset($data['APIConnect']) &&
                strtoupper(
                    trim(
                        (string) $data['APIConnect']
                    )
                ) !== 'DONE'
            ) {
                return [
                    'valid' => false,
                    'reason' =>
                        $data['APIConnect']
                        ??
                        'SSLCommerz API connection failed.',
                    'data' => $data,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Find transaction
            |--------------------------------------------------------------------------
            */

            $transactions = $data['element'] ?? [];

            if (!is_array($transactions)) {
                $transactions = [];
            }

            /*
            |--------------------------------------------------------------------------
            | Some responses can contain a single transaction structure.
            |--------------------------------------------------------------------------
            */

            if (
                isset($transactions['tran_id'])
            ) {
                $transactions = [
                    $transactions,
                ];
            }

            $matchedTransaction = null;

            foreach ($transactions as $transaction) {
                if (!is_array($transaction)) {
                    continue;
                }

                if (
                    isset($transaction['tran_id']) &&
                    (string) $transaction['tran_id'] === $tranId
                ) {
                    $matchedTransaction = $transaction;
                    break;
                }
            }

            if (!$matchedTransaction) {
                return [
                    'valid' => false,
                    'reason' =>
                        'Transaction not found.',
                    'data' => $data,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Transaction status
            |--------------------------------------------------------------------------
            */

            $status = strtoupper(
                trim(
                    (string) (
                        $matchedTransaction['status']
                        ?? ''
                    )
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Only VALID / VALIDATED transactions are accepted
            |--------------------------------------------------------------------------
            */

            if (
                !in_array(
                    $status,
                    [
                        'VALID',
                        'VALIDATED',
                    ],
                    true
                )
            ) {
                return [
                    'valid' => false,
                    'reason' =>
                        $matchedTransaction['status']
                        ??
                        'Transaction is not valid.',
                    'data' => $matchedTransaction,
                ];
            }

            return [
                'valid' => true,
                'data' => $matchedTransaction,
            ];
        } catch (Throwable $e) {
            Log::error(
                'SSLCommerz validation exception',
                [
                    'tran_id' => $tranId,
                    'message' => $e->getMessage(),
                ]
            );

            return [
                'valid' => false,
                'reason' =>
                    'SSLCommerz validation connection failed.',
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ORDER AMOUNT
    |--------------------------------------------------------------------------
    */

    private function getOrderAmount(
        Order $order
    ): float {
        /*
        | Prefer total_amount because that is the existing IBL CART 24
        | order column.
        |
        | Fall back to total for compatibility.
        */

        $amount = $order->total_amount;

        if ($amount === null || $amount === '') {
            $amount = $order->total;
        }

        return round(
            (float) $amount,
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AMOUNT MATCH
    |--------------------------------------------------------------------------
    */

    private function amountMatchesOrder(
        Order $order,
        array $data
    ): bool {
        $gatewayAmount = round(
            (float) (
                $data['amount']
                ?? 0
            ),
            2
        );

        $orderAmount = $this->getOrderAmount(
            $order
        );

        if (
            $gatewayAmount <= 0 ||
            abs(
                $gatewayAmount -
                $orderAmount
            ) > 0.01
        ) {
            Log::warning(
                'SSLCommerz amount mismatch',
                [
                    'order_id' => $order->id,
                    'tran_id' => $order->payment_transaction_id,
                    'order_amount' => $orderAmount,
                    'gateway_amount' => $gatewayAmount,
                ]
            );

            return false;
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CURRENCY VALIDATION
    |--------------------------------------------------------------------------
    */

    private function currencyIsValid(
        array $data
    ): bool {
        /*
        | If currency exists, it must be BDT.
        |
        | Some transaction-query responses may omit it, so absence is
        | accepted for compatibility with existing SSLCommerz responses.
        */

        if (!isset($data['currency'])) {
            return true;
        }

        return strtoupper(
            trim(
                (string) $data['currency']
            )
        ) === 'BDT';
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION ID
    |--------------------------------------------------------------------------
    */

    private function getTransactionId(
        Request $request
    ): string {
        return trim(
            (string) $request->input(
                'tran_id',
                ''
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FIND ORDER BY TRANSACTION ID
    |--------------------------------------------------------------------------
    */

    private function findOrderByTransactionId(
        string $tranId
    ): ?Order {
        return Order::where(
            'payment_transaction_id',
            $tranId
        )->first();
    }


    /*
    |--------------------------------------------------------------------------
    | PAID CHECK
    |--------------------------------------------------------------------------
    */

    private function isPaid(
        Order $order
    ): bool {
        return strtoupper(
            trim(
                (string) $order->payment_status
            )
        ) === 'PAID';
    }


    /*
    |--------------------------------------------------------------------------
    | CANCELLED CHECK
    |--------------------------------------------------------------------------
    */

    private function isCancelled(
        Order $order
    ): bool {
        return strtolower(
            trim(
                (string) $order->status
            )
        ) === 'cancelled';
    }


    /*
    |--------------------------------------------------------------------------
    | STRING LENGTH
    |--------------------------------------------------------------------------
    */

    private function limitLength(
        string $value,
        int $length
    ): string {
        $value = trim($value);

        if (function_exists('mb_substr')) {
            return mb_substr(
                $value,
                0,
                $length
            );
        }

        return substr(
            $value,
            0,
            $length
        );
    }
}



