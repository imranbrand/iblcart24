<h5 class="fw-bold">Delivery charges</h5>
<ul>
    <li>Inside Dhaka: ৳{{ number_format(config('shop.delivery.inside_dhaka'), 0) }}</li>
    <li>Outside Dhaka: ৳{{ number_format(config('shop.delivery.outside_dhaka'), 0) }}</li>
    @if((float) config('shop.delivery.free_above') > 0)
        <li>Free delivery on orders of ৳{{ number_format(config('shop.delivery.free_above'), 0) }} or more</li>
    @endif
</ul>

<h5 class="fw-bold mt-4">Delivery time</h5>
<ul>
    <li>Inside Dhaka: about {{ config('shop.delivery.inside_days') }} working days</li>
    <li>Outside Dhaka: about {{ config('shop.delivery.outside_days') }} working days</li>
</ul>
<p class="text-muted small">These are estimates. Delivery can take longer during holidays, bad weather or other events outside our control.</p>

<h5 class="fw-bold mt-4">Payment</h5>
<p>You can pay by Cash on Delivery, bKash, Nagad or online payment. For bKash and Nagad, please enter the correct Transaction ID at checkout so we can verify your payment.</p>

<h5 class="fw-bold mt-4">Tracking</h5>
<p class="mb-0">You can follow your order status any time on the <a href="{{ route('track.order') }}">Track Order</a> page.</p>
