@extends('layouts.admin')

@section('title', 'Customers')

@section('content')

<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="fas fa-users text-primary me-2"></i>
                Customers
            </h1>

            <p class="text-muted mb-0">
                Manage registered customers and view their order history.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Dashboard
            </a>
        </div>

    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif


    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif


    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        {{-- Total Customers --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small fw-semibold mb-1">
                                TOTAL CUSTOMERS
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ number_format($totalCustomers ?? 0) }}
                            </div>
                        </div>

                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                             style="width:55px;height:55px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Total Orders --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small fw-semibold mb-1">
                                TOTAL ORDERS
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ number_format($totalOrders ?? 0) }}
                            </div>
                        </div>

                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center"
                             style="width:55px;height:55px;">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Total Sales --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small fw-semibold mb-1">
                                TOTAL SALES
                            </div>

                            <div class="fs-3 fw-bold">
                                ৳{{ number_format($totalSales ?? 0, 2) }}
                            </div>
                        </div>

                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center"
                             style="width:55px;height:55px;">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Active Customers --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small fw-semibold mb-1">
                                ACTIVE CUSTOMERS
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ number_format($activeCustomers ?? 0) }}
                            </div>
                        </div>

                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center"
                             style="width:55px;height:55px;">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Customer Table Card --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>
                    <h5 class="mb-1 fw-bold">
                        <i class="fas fa-user-friends me-2 text-primary"></i>
                        Customer List
                    </h5>

                    <small class="text-muted">
                        View and manage registered customers.
                    </small>
                </div>


                {{-- Search --}}
                <form action="{{ route('admin.customers.index') }}"
                      method="GET"
                      class="d-flex"
                      style="min-width:280px;">

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search name or email...">

                        @if(request('search'))
                            <a href="{{ route('admin.customers.index') }}"
                               class="btn btn-outline-secondary"
                               title="Clear search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif

                        <button class="btn btn-primary" type="submit">
                            Search
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <div class="card-body p-0">

            @if($customers->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="px-4">
                                    #
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Email
                                </th>

                                <th class="text-center">
                                    Orders
                                </th>

                                <th class="text-end">
                                    Total Spent
                                </th>

                                <th>
                                    Registered
                                </th>

                                <th class="text-end px-4">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($customers as $customer)

                                <tr>

                                    {{-- ID --}}
                                    <td class="px-4 fw-semibold text-muted">
                                        {{ $customer->id }}
                                    </td>


                                    {{-- Customer --}}
                                    <td>

                                        <div class="d-flex align-items-center">

                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-3"
                                                 style="width:45px;height:45px;">

                                                {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}

                                            </div>

                                            <div>

                                                <div class="fw-semibold">
                                                    {{ $customer->name ?? 'N/A' }}
                                                </div>

                                                @if($customer->phone)
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>
                                                        {{ $customer->phone }}
                                                    </small>
                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Email --}}
                                    <td>

                                        <div>
                                            {{ $customer->email }}
                                        </div>

                                        @if($customer->email_verified_at)
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Verified
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                Not verified
                                            </small>
                                        @endif

                                    </td>


                                    {{-- Orders --}}
                                    <td class="text-center">

                                        <span class="badge rounded-pill bg-primary">
                                            {{ $customer->orders_count ?? 0 }}
                                        </span>

                                    </td>


                                    {{-- Total Spent --}}
                                    <td class="text-end fw-semibold">

                                        ৳{{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}

                                    </td>


                                    {{-- Registered --}}
                                    <td>

                                        <div>
                                            {{ optional($customer->created_at)->format('d M Y') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ optional($customer->created_at)->format('h:i A') }}
                                        </small>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end px-4">

                                        <div class="btn-group">

                                            <a href="{{ route('admin.customers.show', $customer) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Customer">

                                                <i class="fas fa-eye"></i>

                                            </a>


                                            <a href="{{ route('admin.customers.edit', $customer) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Edit Customer">

                                                <i class="fas fa-edit"></i>

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- Empty State --}}
                <div class="text-center py-5 px-3">

                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:80px;height:80px;">

                        <i class="fas fa-users fa-2x text-muted"></i>

                    </div>

                    <h5 class="fw-bold">
                        No Customers Found
                    </h5>

                    @if(request('search'))

                        <p class="text-muted mb-3">
                            No customers matched
                            "<strong>{{ request('search') }}</strong>".
                        </p>

                        <a href="{{ route('admin.customers.index') }}"
                           class="btn btn-outline-primary">

                            <i class="fas fa-times me-1"></i>
                            Clear Search

                        </a>

                    @else

                        <p class="text-muted mb-0">
                            There are currently no registered customers.
                        </p>

                    @endif

                </div>

            @endif

        </div>


        {{-- Pagination --}}
        @if($customers->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                    <div class="text-muted small">

                        Showing
                        <strong>{{ $customers->firstItem() ?? 0 }}</strong>
                        to
                        <strong>{{ $customers->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $customers->total() }}</strong>
                        customers

                    </div>

                    <div>
                        {{ $customers->withQueryString()->links() }}
                    </div>

                </div>

            </div>

        @endif

    </div>

</div>


{{-- Small Custom Styling --}}
<style>

    .card {
        border-radius: 14px;
    }

    .table > :not(caption) > * > * {
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .table thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap;
    }

    .table tbody tr {
        transition: all .15s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, .025);
    }

    .btn {
        border-radius: 8px;
    }

    .input-group .form-control {
        min-height: 40px;
    }

</style>

@endsection