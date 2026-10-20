@extends('layouts.admin')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Categories
            </h2>

            <p class="text-muted mb-0">
                Manage your product categories
            </p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="btn btn-primary">

            <i class="fas fa-plus me-1"></i>
            Add Category

        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            <i class="fas fa-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Error Message --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            <i class="fas fa-exclamation-triangle me-2"></i>

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please fix the following errors:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Categories Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-dark text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">

                    <i class="fas fa-layer-group me-2"></i>

                    Category List

                </h5>

                <span class="badge bg-light text-dark">

                    {{ $categories->total() }}

                    {{ $categories->total() == 1 ? 'Category' : 'Categories' }}

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($categories->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="px-4">
                                    #
                                </th>

                                <th>
                                    Category Name
                                </th>

                                <th>
                                    Slug
                                </th>

                                <th>
                                    Products
                                </th>

                                <th>
                                    Created
                                </th>

                                <th class="text-end px-4">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($categories as $category)

                                <tr>

                                    {{-- ID --}}
                                    <td class="px-4">

                                        <span class="fw-semibold">
                                            {{ $category->id }}
                                        </span>

                                    </td>


                                    {{-- Name --}}
                                    <td>

                                        <div class="d-flex align-items-center">

                                            <div
                                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                style="width:40px;height:40px;"
                                            >
                                                <i class="fas fa-folder"></i>
                                            </div>

                                            <div>

                                                <div class="fw-semibold">
                                                    {{ $category->name }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Slug --}}
                                    <td>

                                        <code>
                                            {{ $category->slug }}
                                        </code>

                                    </td>


                                    {{-- Products Count --}}
                                    <td>

                                        <span class="badge bg-info text-dark">

                                            {{ $category->products_count ?? 0 }}

                                            {{ ($category->products_count ?? 0) == 1 ? 'Product' : 'Products' }}

                                        </span>

                                    </td>


                                    {{-- Created --}}
                                    <td>

                                        <small class="text-muted">

                                            {{ $category->created_at?->format('d M Y') }}

                                        </small>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end px-4">

                                        <div class="d-inline-flex gap-1">

                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('admin.categories.edit', $category) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Category"
                                            >

                                                <i class="fas fa-edit"></i>

                                            </a>


                                            {{-- Delete --}}
                                            <form
                                                action="{{ route('admin.categories.destroy', $category) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Category"
                                                >

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if($categories->hasPages())

                    <div class="p-4">

                        {{ $categories->links() }}

                    </div>

                @endif

            @else

                {{-- Empty State --}}
                <div class="text-center py-5">

                    <div
                        class="mb-3 text-muted"
                        style="font-size:50px;"
                    >
                        <i class="fas fa-folder-open"></i>
                    </div>

                    <h5>
                        No Categories Found
                    </h5>

                    <p class="text-muted">
                        Start by creating your first product category.
                    </p>

                    <a
                        href="{{ route('admin.categories.create') }}"
                        class="btn btn-primary"
                    >

                        <i class="fas fa-plus me-1"></i>

                        Create First Category

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection