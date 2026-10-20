@php
    $faqs = [
        ['How do I place an order?', 'Add products to your cart, go to checkout, enter your name, phone number and address, choose your delivery area and payment method, and confirm the order.'],
        ['Which payment methods do you accept?', 'Cash on Delivery, bKash, Nagad and online payment. For bKash and Nagad, enter the Transaction ID of your payment at checkout so we can verify it.'],
        ['How much is delivery?', 'Inside Dhaka: ৳' . number_format(config('shop.delivery.inside_dhaka'), 0) . '. Outside Dhaka: ৳' . number_format(config('shop.delivery.outside_dhaka'), 0) . '.' . ((float) config('shop.delivery.free_above') > 0 ? ' Orders of ৳' . number_format(config('shop.delivery.free_above'), 0) . ' or more get free delivery.' : '')],
        ['How long does delivery take?', 'About ' . config('shop.delivery.inside_days') . ' working days inside Dhaka and ' . config('shop.delivery.outside_days') . ' working days outside Dhaka.'],
        ['How can I track my order?', 'Open the Track Order page and enter your order details, or log in and open My Orders.'],
        ['Can I cancel my order?', 'You can cancel a pending, unpaid order yourself from the order page. After it is processing or shipped, please contact us.'],
        ['What if I receive a wrong or damaged item?', 'Contact us within ' . config('shop.return_days') . ' days with your order number and photos. See our Return & Refund Policy for details.'],
        ['Can I order the same items again?', 'Yes. Open a past order and press "Order again" to put the items back in your cart at today\'s price.'],
    ];
@endphp

<div class="accordion accordion-flush" id="faqAccordion">
    @foreach($faqs as $i => $faq)
        <div class="accordion-item">
            <h2 class="accordion-header" id="faq-h-{{ $i }}">
                <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq-c-{{ $i }}"
                        aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faq-c-{{ $i }}">
                    {{ $faq[0] }}
                </button>
            </h2>
            <div id="faq-c-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                 aria-labelledby="faq-h-{{ $i }}" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">{{ $faq[1] }}</div>
            </div>
        </div>
    @endforeach
</div>

<p class="mt-4 mb-0">Still have a question? <a href="{{ route('pages.contact') }}">Contact us</a>.</p>
