@extends('layouts.admin')

@section('title', 'Create Coupon')

@section('content')
<div class="container-fluid py-4">
    <h3 class="fw-bold mb-4">Create Coupon</h3>

    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            @include('admin.coupons._form')
            <div class="mt-4">
                <button class="btn btn-primary">Save Coupon</button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
