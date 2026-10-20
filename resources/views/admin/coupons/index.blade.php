@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Coupons</h3>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> New Coupon
        </a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th><th>Discount</th><th>Min order</th><th>Used</th>
                        <th>Expires</th><th>Status</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td class="fw-bold">{{ $coupon->code }}</td>
                        <td>
                            @if($coupon->type === 'percent') {{ rtrim(rtrim($coupon->value, '0'), '.') }}%
                            @else ৳{{ number_format($coupon->value, 2) }} @endif
                        </td>
                        <td>৳{{ number_format($coupon->min_order_amount, 2) }}</td>
                        <td>{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}</td>
                        <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : '—' }}</td>
                        <td>
                            @if($coupon->isCurrentlyValid())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this coupon?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No coupons yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $coupons->links() }}</div>
</div>
@endsection
