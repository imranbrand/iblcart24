{{-- =========================================================
     IBL CART 24 - RELATED PRODUCTS
========================================================= --}}

@php
    $relatedProducts = $relatedProducts ?? collect();

    if ($relatedProducts->isEmpty() && isset($product)) {
        $relatedProducts = \App\Models\Product::with('category')
            ->where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->when(
                $product->category_id,
                function ($query) use ($product) {
                    $query->where('category_id', $product->category_id);
                }
            )
            ->latest()
            ->take(4)
            ->get();
    }
@endphp

@if($relatedProducts->isNotEmpty())

<section class="related-products-section py-5">

    <div class="container">

        <div class="text-center mb-4">

            <span class="related-eyebrow">
                <i class="fa-solid fa-sparkles"></i>
                YOU MAY ALSO LIKE
            </span>

            <h2 class="related-title">
                Related Products
            </h2>

            <p class="related-subtitle">
                Discover more premium products from IBL CART 24.
            </p>

        </div>

        <div class="row g-4">

            @foreach($relatedProducts as $related)

                @php
                    $image = $related->image
                        ? asset('uploads/products/' . $related->image)
                        : asset('images/no-product-image.png');

                    $stock = (int) ($related->stock ?? 0);

                    $categoryName = optional($related->category)->name
                        ?? 'Premium Collection';
                @endphp

                <div class="col-6 col-md-4 col-lg-3">

                    <div class="related-card">

                        <div class="related-image">

                            <a href="{{ route('products.show', $related->id) }}">

                                <img
                                    src="{{ $image }}"
                                    alt="{{ $related->name }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/no-product-image.png') }}';"
                                >

                            </a>

                            @if($stock <= 5)

                                <span class="related-stock low">
                                    Only {{ $stock }} left
                                </span>

                            @else

                                <span class="related-stock">
                                    In Stock
                                </span>

                            @endif

                            <a
                                href="{{ route('products.show', $related->id) }}"
                                class="related-eye"
                                title="View Product"
                            >
                                <i class="fa-regular fa-eye"></i>
                            </a>

                        </div>

                        <div class="related-content">

                            <div class="related-category">
                                {{ $categoryName }}
                            </div>

                            <h3>
                                <a href="{{ route('products.show', $related->id) }}">
                                    {{ $related->name }}
                                </a>
                            </h3>

                            <div class="related-price">
                                ৳{{ number_format((float) $related->price, 2) }}
                            </div>

                            <a
                                href="{{ route('products.show', $related->id) }}"
                                class="related-button"
                            >
                                <i class="fa-solid fa-cart-shopping"></i>
                                View Product
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</section>

@endif


<style>

.related-products-section {
    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #fafafa 100%
    );

    border-top: 1px solid rgba(0,0,0,.06);
}

.related-eyebrow {
    display: inline-block;

    padding: 7px 14px;

    border-radius: 50px;

    background: rgba(212,175,55,.12);

    color: #9b7618;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 1.4px;

    text-transform: uppercase;
}

.related-title {
    margin-top: 12px;
    margin-bottom: 6px;

    color: #171717;

    font-size: 32px;

    font-weight: 800;
}

.related-subtitle {
    color: #777;

    font-size: 15px;

    margin-bottom: 0;
}

.related-card {
    height: 100%;

    overflow: hidden;

    background: #fff;

    border: 1px solid rgba(0,0,0,.07);

    border-radius: 18px;

    box-shadow: 0 8px 30px rgba(0,0,0,.06);

    transition:
        transform .3s ease,
        box-shadow .3s ease;
}

.related-card:hover {
    transform: translateY(-7px);

    box-shadow: 0 18px 45px rgba(0,0,0,.12);
}

.related-image {
    position: relative;

    overflow: hidden;

    aspect-ratio: 1 / 1;

    background: #f3f3f3;
}

.related-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;

    transition: transform .5s ease;
}

.related-card:hover .related-image img {
    transform: scale(1.06);
}

.related-stock {
    position: absolute;

    top: 12px;
    left: 12px;

    padding: 6px 10px;

    border-radius: 50px;

    background: rgba(255,255,255,.95);

    color: #198754;

    font-size: 11px;

    font-weight: 800;
}

.related-stock.low {
    color: #b26a00;
}

.related-eye {
    position: absolute;

    top: 12px;
    right: 12px;

    width: 38px;
    height: 38px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: rgba(255,255,255,.95);

    color: #222;

    text-decoration: none;

    opacity: 0;

    transition: .25s ease;
}

.related-card:hover .related-eye {
    opacity: 1;
}

.related-eye:hover {
    background: #111;
    color: #fff;
}

.related-content {
    padding: 16px;
}

.related-category {
    margin-bottom: 6px;

    color: #a17d20;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1px;

    text-transform: uppercase;
}

.related-content h3 {
    min-height: 46px;

    margin: 0 0 8px;

    font-size: 16px;

    line-height: 1.45;
}

.related-content h3 a {
    color: #171717;

    text-decoration: none;
}

.related-content h3 a:hover {
    color: #b28a22;
}

.related-price {
    margin-bottom: 13px;

    color: #111;

    font-size: 19px;

    font-weight: 800;
}

.related-button {
    display: block;

    width: 100%;

    padding: 9px 12px;

    border-radius: 10px;

    background: #111;

    border: 1px solid #111;

    color: #fff;

    text-align: center;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    transition: .25s ease;
}

.related-button:hover {
    background: #d4af37;

    border-color: #d4af37;

    color: #111;
}

@media (max-width: 575px) {

    .related-title {
        font-size: 25px;
    }

    .related-content {
        padding: 12px;
    }

    .related-content h3 {
        font-size: 14px;
    }

    .related-price {
        font-size: 16px;
    }

    .related-button {
        font-size: 11px;
    }

    .related-eye {
        opacity: 1;
        width: 34px;
        height: 34px;
    }
}

</style>
