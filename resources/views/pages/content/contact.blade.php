<p>We are happy to help with your orders and questions.</p>

@php
    $hasContact = config('shop.phone') || config('shop.whatsapp') || config('shop.email') || config('shop.address') || config('shop.hours');
@endphp

@if($hasContact)
    <ul class="list-unstyled mb-4">
        @if(config('shop.phone'))
            <li class="mb-2"><i class="fa-solid fa-phone me-2 text-primary"></i><a href="tel:{{ config('shop.phone') }}">{{ config('shop.phone') }}</a></li>
        @endif
        @if(config('shop.whatsapp'))
            <li class="mb-2"><i class="fa-brands fa-whatsapp me-2 text-success"></i><a href="https://wa.me/{{ \App\Models\Setting::whatsappNumber() }}" target="_blank" rel="noopener">WhatsApp: {{ config('shop.whatsapp') }}</a></li>
        @endif
        @if(config('shop.email'))
            <li class="mb-2"><i class="fa-regular fa-envelope me-2 text-primary"></i><a href="mailto:{{ config('shop.email') }}">{{ config('shop.email') }}</a></li>
        @endif
        @if(config('shop.address'))
            <li class="mb-2"><i class="fa-solid fa-location-dot me-2 text-danger"></i>{{ config('shop.address') }}</li>
        @endif
        @if(config('shop.hours'))
            <li class="mb-2"><i class="fa-regular fa-clock me-2 text-secondary"></i>{{ config('shop.hours') }}</li>
        @endif
    </ul>
@else
    @auth
        @if(auth()->user()->isAdmin())
            <div class="alert alert-warning">
                Contact details are not set yet. Add them in
                <a href="{{ route('admin.settings.edit') }}">Admin &gt; Settings</a>.
                (Only admins see this message.)
            </div>
        @endif
    @endauth
@endif

<p class="mb-0">
    For order updates, please use <a href="{{ route('track.order') }}">Track Your Order</a>.
    When you contact us about an order, keep your order number ready.
</p>
