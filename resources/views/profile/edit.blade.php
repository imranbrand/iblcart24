@extends('layouts.storefront')

@section('content')

<style>
    .profile-page {
        min-height: calc(100vh - 100px);
        background: #f8f9fa;
        padding: 45px 0 80px;
    }

    .profile-header {
        margin-bottom: 30px;
    }

    .profile-title {
        font-size: 34px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 7px;
    }

    .profile-subtitle {
        color: #6b7280;
        margin-bottom: 0;
    }

    .profile-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .profile-card-header {
        padding: 22px 25px;
        border-bottom: 1px solid #eef0f2;
    }

    .profile-card-header h2 {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 5px;
    }

    .profile-card-header p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .profile-card-body {
        padding: 25px;
    }

    .profile-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #111827;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .profile-name {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 4px;
    }

    .profile-email {
        color: #6b7280;
        font-size: 14px;
        word-break: break-word;
    }

    .form-label {
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control {
        min-height: 47px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 10px 14px;
        box-shadow: none !important;
    }

    .form-control:focus {
        border-color: #111827;
    }

    .form-control.is-invalid {
        border-color: #dc2626;
    }

    .form-text {
        color: #6b7280;
        font-size: 12px;
        margin-top: 6px;
    }

    .btn-profile {
        min-height: 45px;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 700;
        border: none;
        transition: all .2s ease;
    }

    .btn-primary-custom {
        background: #111827;
        color: #ffffff;
    }

    .btn-primary-custom:hover {
        background: #000000;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-danger-custom {
        background: #dc2626;
        color: #ffffff;
    }

    .btn-danger-custom:hover {
        background: #b91c1c;
        color: #ffffff;
    }

    .danger-card {
        border-color: #fecaca;
    }

    .danger-card .profile-card-header {
        background: #fff7f7;
        border-bottom-color: #fee2e2;
    }

    .success-message {
        color: #15803d;
        font-size: 13px;
        font-weight: 600;
        margin-top: 12px;
    }

    .status-icon {
        margin-right: 6px;
    }

    .error-list {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 12px;
        padding: 15px 18px;
        margin-bottom: 25px;
    }

    .error-list ul {
        margin: 0;
        padding-left: 20px;
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper .form-control {
        padding-right: 48px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 13px;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        z-index: 2;
    }

    .password-toggle:hover {
        color: #111827;
    }

    .account-info {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 20px;
        background: #f9fafb;
        border-radius: 14px;
        margin-bottom: 25px;
    }

    .account-info-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: #e5e7eb;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .account-info-title {
        font-weight: 700;
        color: #111827;
        margin-bottom: 3px;
    }

    .account-info-text {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    @media (max-width: 767.98px) {

        .profile-page {
            padding: 30px 0 60px;
        }

        .profile-title {
            font-size: 28px;
        }

        .profile-card-body,
        .profile-card-header {
            padding: 20px;
        }

        .account-info {
            align-items: flex-start;
        }
    }
</style>


<div class="profile-page">

    <div class="container">

        {{-- Page Header --}}
        <div class="profile-header">

            <h1 class="profile-title">
                <i class="fa-regular fa-user me-2"></i>
                My Profile
            </h1>

            <p class="profile-subtitle">
                Manage your personal information, password and account settings.
            </p>

        </div>


        {{-- Validation Errors --}}
        @if ($errors->any())

            <div class="error-list">

                <strong>
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    Please fix the following errors:
                </strong>

                <ul class="mt-2">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Profile Updated Message --}}
        @if (session('status') === 'profile-updated')

            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <i class="fa-solid fa-circle-check me-2"></i>

                Your profile information has been updated successfully.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
                </button>

            </div>

        @endif


        {{-- Password Updated Message --}}
        @if (session('status') === 'password-updated')

            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <i class="fa-solid fa-circle-check me-2"></i>

                Your password has been updated successfully.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
                </button>

            </div>

        @endif


        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-4">

                {{-- Account Summary --}}
                <div class="profile-card">

                    <div class="profile-card-body text-center">

                        @php
                            $userName = $user->name ?? 'User';

                            $initials = collect(
                                preg_split('/\s+/', trim($userName))
                            )
                            ->filter()
                            ->take(2)
                            ->map(function ($part) {
                                return strtoupper(substr($part, 0, 1));
                            })
                            ->implode('');
                        @endphp


                        <div class="profile-avatar mx-auto">
                            {{ $initials ?: 'U' }}
                        </div>


                        <div class="profile-name">
                            {{ $user->name }}
                        </div>


                        <div class="profile-email">
                            {{ $user->email }}
                        </div>

                    </div>

                </div>


                {{-- Account Information --}}
                <div class="profile-card">

                    <div class="profile-card-header">

                        <h2>
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Account Information
                        </h2>

                        <p>
                            Your account details.
                        </p>

                    </div>


                    <div class="profile-card-body">

                        <div class="account-info">

                            <div class="account-info-icon">
                                <i class="fa-regular fa-user"></i>
                            </div>

                            <div>

                                <div class="account-info-title">
                                    Name
                                </div>

                                <p class="account-info-text">
                                    {{ $user->name }}
                                </p>

                            </div>

                        </div>


                        <div class="account-info">

                            <div class="account-info-icon">
                                <i class="fa-regular fa-envelope"></i>
                            </div>

                            <div>

                                <div class="account-info-title">
                                    Email
                                </div>

                                <p class="account-info-text">
                                    {{ $user->email }}
                                </p>

                            </div>

                        </div>


                        <div class="account-info mb-0">

                            <div class="account-info-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </div>

                            <div>

                                <div class="account-info-title">
                                    Member Since
                                </div>

                                <p class="account-info-text">

                                    @if($user->created_at)
                                        {{ $user->created_at->format('d M Y') }}
                                    @else
                                        N/A
                                    @endif

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RIGHT COLUMN --}}
            <div class="col-lg-8">

                {{-- Update Profile --}}
                <div class="profile-card">

                    <div class="profile-card-header">

                        <h2>
                            <i class="fa-regular fa-id-card me-2"></i>
                            Profile Information
                        </h2>

                        <p>
                            Update your name and email address.
                        </p>

                    </div>


                    <div class="profile-card-body">

                        <form
                            method="POST"
                            action="{{ route('profile.update') }}"
                        >

                            @csrf
                            @method('PATCH')


                            {{-- Name --}}
                            <div class="mb-4">

                                <label
                                    for="name"
                                    class="form-label"
                                >
                                    Full Name
                                </label>

                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    autofocus
                                    autocomplete="name"
                                >

                                @error('name')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- Email --}}
                            <div class="mb-4">

                                <label
                                    for="email"
                                    class="form-label"
                                >
                                    Email Address
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    autocomplete="username"
                                >

                                @error('email')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            <button
                                type="submit"
                                class="btn-profile btn-primary-custom"
                            >
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Save Profile
                            </button>

                        </form>

                    </div>

                </div>


                {{-- Change Password --}}
                <div class="profile-card">

                    <div class="profile-card-header">

                        <h2>
                            <i class="fa-solid fa-lock me-2"></i>
                            Update Password
                        </h2>

                        <p>
                            Use a strong password to keep your account secure.
                        </p>

                    </div>


                    <div class="profile-card-body">

                        <form
                            method="POST"
                            action="{{ route('password.update') }}"
                        >

                            @csrf

                            @method('PUT')


                            {{-- Current Password --}}
                            <div class="mb-4">

                                <label
                                    for="current_password"
                                    class="form-label"
                                >
                                    Current Password
                                </label>

                                <div class="password-wrapper">

                                    <input
                                        id="current_password"
                                        name="current_password"
                                        type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        required
                                        autocomplete="current-password"
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        onclick="togglePassword('current_password', this)"
                                        aria-label="Show current password"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                </div>

                                @error('current_password')

                                    <div class="text-danger small mt-2">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- New Password --}}
                            <div class="mb-4">

                                <label
                                    for="password"
                                    class="form-label"
                                >
                                    New Password
                                </label>

                                <div class="password-wrapper">

                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        onclick="togglePassword('password', this)"
                                        aria-label="Show new password"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                </div>

                                <div class="form-text">
                                    Password must contain at least 8 characters.
                                </div>

                                @error('password')

                                    <div class="text-danger small mt-2">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- Confirm Password --}}
                            <div class="mb-4">

                                <label
                                    for="password_confirmation"
                                    class="form-label"
                                >
                                    Confirm New Password
                                </label>

                                <div class="password-wrapper">

                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        class="form-control"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        onclick="togglePassword('password_confirmation', this)"
                                        aria-label="Show password confirmation"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                </div>

                            </div>


                            <button
                                type="submit"
                                class="btn-profile btn-primary-custom"
                            >
                                <i class="fa-solid fa-key me-2"></i>
                                Update Password
                            </button>

                        </form>

                    </div>

                </div>


                {{-- Delete Account --}}
                <div class="profile-card danger-card">

                    <div class="profile-card-header">

                        <h2 class="text-danger">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Delete Account
                        </h2>

                        <p>
                            Permanently delete your account and associated information.
                        </p>

                    </div>


                    <div class="profile-card-body">

                        <p class="text-muted small mb-4">
                            Once your account is deleted, all of its resources and data
                            may be permanently removed. Please make sure you are certain
                            before proceeding.
                        </p>


                        <form
                            method="POST"
                            action="{{ route('profile.destroy') }}"
                            onsubmit="return confirmDeleteAccount()"
                        >

                            @csrf
                            @method('DELETE')


                            <div class="mb-4">

                                <label
                                    for="delete_password"
                                    class="form-label"
                                >
                                    Confirm Your Password
                                </label>

                                <div class="password-wrapper">

                                    <input
                                        id="delete_password"
                                        name="password"
                                        type="password"
                                        class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                        required
                                        autocomplete="current-password"
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        onclick="togglePassword('delete_password', this)"
                                        aria-label="Show password"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                </div>

                                @error('password', 'userDeletion')

                                    <div class="text-danger small mt-2">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            <button
                                type="submit"
                                class="btn-profile btn-danger-custom"
                            >
                                <i class="fa-solid fa-trash-can me-2"></i>
                                Delete My Account
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
    function togglePassword(inputId, button) {

        const input = document.getElementById(inputId);

        if (!input) {
            return;
        }

        const icon = button.querySelector('i');

        if (input.type === 'password') {

            input.type = 'text';

            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }

            button.setAttribute(
                'aria-label',
                'Hide password'
            );

        } else {

            input.type = 'password';

            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }

            button.setAttribute(
                'aria-label',
                'Show password'
            );
        }
    }


    function confirmDeleteAccount() {

        return confirm(
            'Are you sure you want to permanently delete your account? This action cannot be undone.'
        );
    }
</script>

@endsection