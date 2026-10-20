@extends('layouts.admin')

@section('title', 'Shop Settings')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px;">

    <h3 class="fw-bold mb-4">Shop Settings</h3>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger">Please fix the highlighted fields.</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        {{-- ---------------- Delivery ---------------- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-truck-fast me-2"></i>Delivery</h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Inside Dhaka charge (৳)</label>
                        <input type="number" step="0.01" min="0" name="delivery_inside_dhaka"
                               value="{{ old('delivery_inside_dhaka', config('shop.delivery.inside_dhaka')) }}"
                               class="form-control @error('delivery_inside_dhaka') is-invalid @enderror" required>
                        @error('delivery_inside_dhaka') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Outside Dhaka charge (৳)</label>
                        <input type="number" step="0.01" min="0" name="delivery_outside_dhaka"
                               value="{{ old('delivery_outside_dhaka', config('shop.delivery.outside_dhaka')) }}"
                               class="form-control @error('delivery_outside_dhaka') is-invalid @enderror" required>
                        @error('delivery_outside_dhaka') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Free delivery from (৳)</label>
                        <input type="number" step="0.01" min="0" name="free_delivery_above"
                               value="{{ old('free_delivery_above', config('shop.delivery.free_above')) }}"
                               class="form-control @error('free_delivery_above') is-invalid @enderror">
                        <div class="form-text">0 = free delivery is off.</div>
                        @error('free_delivery_above') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Inside Dhaka time (days)</label>
                        <input type="text" name="delivery_days_inside" maxlength="20"
                               value="{{ old('delivery_days_inside', config('shop.delivery.inside_days')) }}"
                               class="form-control" placeholder="1-2">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Outside Dhaka time (days)</label>
                        <input type="text" name="delivery_days_outside" maxlength="20"
                               value="{{ old('delivery_days_outside', config('shop.delivery.outside_days')) }}"
                               class="form-control" placeholder="3-5">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Return window (days)</label>
                        <input type="number" min="0" max="365" name="return_days"
                               value="{{ old('return_days', config('shop.return_days')) }}"
                               class="form-control @error('return_days') is-invalid @enderror" required>
                        @error('return_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------------- Contact ---------------- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-address-book me-2"></i>Shop &amp; contact info</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Shop name</label>
                        <input type="text" name="name" maxlength="100" required
                               value="{{ old('name', config('shop.name')) }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" maxlength="150"
                               value="{{ old('email', config('shop.email')) }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" maxlength="30"
                               value="{{ old('phone', config('shop.phone')) }}"
                               class="form-control" placeholder="01XXXXXXXXX">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">WhatsApp number</label>
                        <input type="text" name="whatsapp" maxlength="30"
                               value="{{ old('whatsapp', config('shop.whatsapp')) }}"
                               class="form-control" placeholder="01XXXXXXXXX">
                        <div class="form-text">The green WhatsApp button appears on the site when this is filled.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" maxlength="255"
                               value="{{ old('address', config('shop.address')) }}"
                               class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Opening hours</label>
                        <input type="text" name="hours" maxlength="150"
                               value="{{ old('hours', config('shop.hours')) }}"
                               class="form-control" placeholder="Sat-Thu, 10:00 AM - 8:00 PM">
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------------- Announcement bar ---------------- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-bullhorn me-2"></i>Announcement bar</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="announcement_enabled"
                           name="announcement_enabled" value="1"
                           {{ old('announcement_enabled', config('shop.announcement_enabled', false)) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="announcement_enabled">Show the bar at the top of the shop</label>
                </div>

                <input type="text" name="announcement_text" maxlength="200"
                       value="{{ old('announcement_text', config('shop.announcement_text')) }}"
                       class="form-control @error('announcement_text') is-invalid @enderror"
                       placeholder="Eid sale: free delivery on orders above ৳3000">
                @error('announcement_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ---------------- Reviews ---------------- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-star me-2"></i>Reviews</h5>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="reviews_require_approval"
                           name="reviews_require_approval" value="1"
                           {{ old('reviews_require_approval', config('shop.reviews_require_approval', false)) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="reviews_require_approval">
                        New reviews need my approval before they are shown
                    </label>
                </div>
                <div class="form-text">Approve or hide reviews from the Reviews page in the sidebar.</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save settings
        </button>
    </form>
</div>
@endsection
