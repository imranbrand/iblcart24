@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <div class="d-flex align-items-center gap-2 mb-1">

                <a
                    href="{{ route('admin.customers.index') }}"
                    class="text-decoration-none text-secondary"
                >
                    <i class="fas fa-users me-1"></i>
                    Customers
                </a>

                <i class="fas fa-chevron-right text-muted small"></i>

                <a
                    href="{{ route('admin.customers.show', $user) }}"
                    class="text-decoration-none text-secondary"
                >
                    {{ $user->name }}
                </a>

                <i class="fas fa-chevron-right text-muted small"></i>

                <span class="text-muted">
                    Edit
                </span>

            </div>

            <h2 class="fw-bold mb-1">
                <i class="fas fa-user-edit text-primary me-2"></i>
                Edit Customer
            </h2>

            <p class="text-muted mb-0">
                Update customer account information.
            </p>

        </div>

        <a
            href="{{ route('admin.customers.show', $user) }}"
            class="btn btn-outline-secondary"
        >
            <i class="fas fa-arrow-left me-1"></i>
            Back to Customer
        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger shadow-sm">

            <div class="fw-bold mb-2">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please correct the following errors:
            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Form --}}
    <div class="row justify-content-center">

        <div class="col-xl-8 col-lg-10">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-1">
                        Customer Information
                    </h5>

                    <p class="text-muted small mb-0">
                        Customer ID: #{{ $user->id }}
                    </p>

                </div>


                <div class="card-body px-4 pb-4">

                    <form
                        action="{{ route('admin.customers.update', ['user' => $user->id]) }}"
                        method="POST"
                    >

                        @csrf

                        @method('PUT')


                        {{-- Name --}}
                        <div class="mb-4">

                            <label
                                for="name"
                                class="form-label fw-semibold"
                            >
                                Full Name
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user text-primary"></i>
                                </span>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="Enter customer name"
                                    required
                                >

                            </div>

                            @error('name')

                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- Email --}}
                        <div class="mb-4">

                            <label
                                for="email"
                                class="form-label fw-semibold"
                            >
                                Email Address
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="customer@example.com"
                                    required
                                >

                            </div>

                            @error('email')

                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- Phone --}}
                        <div class="mb-4">

                            <label
                                for="phone"
                                class="form-label fw-semibold"
                            >
                                Phone Number
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone text-primary"></i>
                                </span>

                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}"
                                    placeholder="01XXXXXXXXX"
                                >

                            </div>

                            @error('phone')

                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- Password --}}
                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    for="password"
                                    class="form-label fw-semibold"
                                >
                                    New Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-lock text-primary"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Leave blank to keep current"
                                    >

                                </div>

                                @error('password')

                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- Confirm Password --}}
                            <div class="col-md-6">

                                <label
                                    for="password_confirmation"
                                    class="form-label fw-semibold"
                                >
                                    Confirm New Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-lock text-primary"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Repeat new password"
                                    >

                                </div>

                            </div>

                        </div>


                        {{-- Notice --}}
                        <div class="alert alert-info mt-4 mb-4">

                            <i class="fas fa-info-circle me-2"></i>

                            Leave the password fields empty if you do not want
                            to change the customer's current password.

                        </div>


                        {{-- Buttons --}}
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">

                            <a
                                href="{{ route('admin.customers.show', $user) }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                            >
                                <i class="fas fa-save me-1"></i>
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection