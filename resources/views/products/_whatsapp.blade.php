@if(\App\Models\Setting::whatsappNumber() !== '')
    @php
        $waText = 'Hello ' . config('shop.name') . ', I am interested in: ' . $product->name . ' (' . route('products.show', $product) . ')';
    @endphp

    <div class="card border-0 shadow-sm mt-4" style="border-radius:16px;">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="fw-bold">Questions, or want to order by chat?</div>
                <div class="text-muted small">Message us on WhatsApp about this product.</div>
            </div>

            <a
                href="https://wa.me/{{ \App\Models\Setting::whatsappNumber() }}?text={{ rawurlencode($waText) }}"
                target="_blank"
                rel="noopener"
                class="btn btn-success"
            >
                <i class="fa-brands fa-whatsapp me-1"></i>
                Order on WhatsApp
            </a>
        </div>
    </div>
@endif
