@if(isset($recentlyViewed) && $recentlyViewed->count())
<div class="mt-5" id="recently-viewed">
    <h4 class="fw-bold mb-3">Recently viewed</h4>

    <div class="row g-3">
        @foreach($recentlyViewed as $rv)
            <div class="col-6 col-md-3">
                <a href="{{ route('products.show', $rv) }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm h-100" style="border-radius:16px;overflow:hidden;">
                        @if($rv->image)
                            <img src="{{ asset('uploads/products/' . $rv->image) }}" alt="{{ $rv->name }}" loading="lazy"
                                 style="width:100%;height:150px;object-fit:cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height:150px;">
                                <i class="fa-solid fa-image fa-2x"></i>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="fw-semibold small mb-1">{{ \Illuminate\Support\Str::limit($rv->name, 40) }}</div>
                            <div class="fw-bold text-primary">
                                ৳{{ number_format($rv->price, 0) }}
                                @if($rv->is_on_sale)
                                    <small class="text-muted text-decoration-line-through ms-1">৳{{ number_format($rv->compare_price, 0) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif
