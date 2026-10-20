@extends('layouts.admin')

@section('title', 'Blocked Phones')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Blocked Phone Numbers</h3>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blocked-phones.store') }}" class="row g-2 align-items-start">
                @csrf
                <div class="col-md-3">
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="01XXXXXXXXX" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <input type="text" name="reason" value="{{ old('reason') }}" class="form-control" placeholder="Reason (optional), e.g. refused 3 parcels">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-danger w-100"><i class="fa-solid fa-ban me-1"></i> Block number</button>
                </div>
            </form>
            <p class="text-muted small mt-3 mb-0">
                Automatic rules also run at checkout: too many orders from one phone in 24 hours is refused,
                and a phone with several recent cancelled orders must pay by bKash, Nagad or Online Payment.
                Limits are set in <code>.env</code> (FRAUD_MAX_ORDERS_PER_DAY, FRAUD_MAX_CANCELLED).
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Phone</th><th>Reason</th><th>Blocked on</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                @forelse($phones as $row)
                    <tr>
                        <td class="fw-bold">{{ $row->phone }}</td>
                        <td>{{ $row->reason ?: '-' }}</td>
                        <td>{{ $row->created_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.blocked-phones.destroy', $row) }}" onsubmit="return confirm('Unblock this number?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-secondary">Unblock</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No blocked numbers yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $phones->links() }}</div>
</div>
@endsection
