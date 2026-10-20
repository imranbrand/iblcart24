@php $c = $coupon ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Code</label>
        <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
               value="{{ old('code', $c->code ?? '') }}" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Type</label>
        <select name="type" class="form-select">
            <option value="percent" @selected(old('type', $c->type ?? 'percent') === 'percent')>Percent (%)</option>
            <option value="fixed" @selected(old('type', $c->type ?? '') === 'fixed')>Fixed (৳)</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Value</label>
        <input type="number" step="0.01" min="0.01" name="value" class="form-control @error('value') is-invalid @enderror"
               value="{{ old('value', $c->value ?? '') }}" required>
        @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Minimum order (৳)</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control"
               value="{{ old('min_order_amount', $c->min_order_amount ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Max discount (৳, for %)</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control"
               value="{{ old('max_discount_amount', $c->max_discount_amount ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Max uses</label>
        <input type="number" min="1" name="max_uses" class="form-control"
               value="{{ old('max_uses', $c->max_uses ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Starts at</label>
        <input type="datetime-local" name="starts_at" class="form-control"
               value="{{ old('starts_at', optional($c->starts_at ?? null)->format('Y-m-d\TH:i')) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Expires at</label>
        <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ old('expires_at', optional($c->expires_at ?? null)->format('Y-m-d\TH:i')) }}">
        @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                   @checked(old('is_active', $c->is_active ?? true))>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
    </div>
</div>
