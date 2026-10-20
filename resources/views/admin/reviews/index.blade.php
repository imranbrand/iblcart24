@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">Reviews</h3>

        <div class="btn-group" role="group" aria-label="Filter">
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm {{ $status === 'all' ? 'btn-dark' : 'btn-outline-dark' }}">All ({{ $counts['all'] }})</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'visible']) }}" class="btn btn-sm {{ $status === 'visible' ? 'btn-dark' : 'btn-outline-dark' }}">Visible ({{ $counts['visible'] }})</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'hidden']) }}" class="btn btn-sm {{ $status === 'hidden' ? 'btn-dark' : 'btn-outline-dark' }}">Hidden / pending ({{ $counts['hidden'] }})</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    @if(config('shop.reviews_require_approval'))
        <div class="alert alert-info py-2">New reviews need approval. Use <strong>Approve</strong> to show them in the shop.</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th style="min-width:260px;">Review</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>
                            @if($review->product)
                                <a href="{{ route('products.show', $review->product) }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($review->product->name, 36) }}</a>
                            @else
                                <span class="text-muted">Deleted product</span>
                            @endif
                        </td>
                        <td>{{ $review->user->name ?? 'Deleted user' }}</td>
                        <td class="text-warning text-nowrap">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                            @endfor
                        </td>
                        <td>
                            @if($review->title)<div class="fw-semibold">{{ $review->title }}</div>@endif
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($review->comment, 160) ?: '—' }}</div>
                        </td>
                        <td class="text-nowrap small">{{ $review->created_at->format('d M Y') }}</td>
                        <td>
                            @if($review->is_approved)
                                <span class="badge bg-success">Visible</span>
                            @else
                                <span class="badge bg-warning text-dark">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $review->is_approved ? 'btn-outline-secondary' : 'btn-success' }}">
                                    {{ $review->is_approved ? 'Hide' : 'Approve' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this review permanently?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No reviews found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $reviews->links() }}</div>
</div>
@endsection
