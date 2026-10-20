@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<div class="container-fluid py-4">
    <h3 class="fw-bold mb-4">Edit Coupon</h3>

    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
            @csrf
            @method('PUT')
            @include('admin.coupons._form')
            <div class="mt-4">
                <button class="btn btn-primary">Update Coupon</button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
