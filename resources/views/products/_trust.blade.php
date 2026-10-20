<div class="row g-2 mt-4 text-center">
    <div class="col-6 col-md-3">
        <div class="border rounded-3 p-3 h-100 bg-white">
            <i class="fa-solid fa-truck-fast text-primary fs-4 mb-2"></i>
            <div class="fw-semibold small">Fast delivery</div>
            <div class="text-muted" style="font-size:.78rem;">Dhaka {{ config('shop.delivery.inside_days') }} days<br>Outside {{ config('shop.delivery.outside_days') }} days</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="border rounded-3 p-3 h-100 bg-white">
            <i class="fa-solid fa-hand-holding-dollar text-success fs-4 mb-2"></i>
            <div class="fw-semibold small">Cash on Delivery</div>
            <div class="text-muted" style="font-size:.78rem;">Pay when you receive<br>bKash &amp; Nagad too</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="border rounded-3 p-3 h-100 bg-white">
            <i class="fa-solid fa-rotate-left text-warning fs-4 mb-2"></i>
            <div class="fw-semibold small">Easy returns</div>
            <div class="text-muted" style="font-size:.78rem;">{{ config('shop.return_days') }}-day return window<br><a href="{{ route('pages.returns') }}">See policy</a></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="border rounded-3 p-3 h-100 bg-white">
            <i class="fa-solid fa-headset text-danger fs-4 mb-2"></i>
            <div class="fw-semibold small">Real support</div>
            <div class="text-muted" style="font-size:.78rem;">Talk to a real person<br><a href="{{ route('pages.contact') }}">Contact us</a></div>
        </div>
    </div>
</div>
