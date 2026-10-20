@extends('layouts.admin')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Page Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1 fw-bold">Edit Category</h2>
                    <p class="text-muted mb-0">
                        Update category information
                    </p>
                </div>

                <a href="{{ route('admin.categories.index') }}"
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back
                </a>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Please fix the following errors:
                    </strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Edit Form --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Category Information
                    </h5>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('admin.categories.update', $category) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        {{-- Category Name --}}
                        <div class="mb-4">

                            <label for="name"
                                   class="form-label fw-semibold">
                                Category Name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $category->name) }}"
                                placeholder="Enter category name"
                                required
                                maxlength="255"
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Category Slug --}}
                        <div class="mb-4">

                            <label for="slug"
                                   class="form-label fw-semibold">
                                Slug
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $category->slug) }}"
                                placeholder="example-category"
                                required
                                maxlength="255"
                            >

                            <div class="form-text">
                                Use lowercase letters, numbers and hyphens.
                                Example: jewellery
                            </div>

                            @error('slug')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Current Information --}}
                        <div class="alert alert-light border mb-4">

                            <div class="row">

                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Category ID
                                    </small>

                                    <strong>
                                        #{{ $category->id }}
                                    </strong>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Created
                                    </small>

                                    <strong>
                                        {{ $category->created_at?->format('d M Y, h:i A') }}
                                    </strong>
                                </div>

                            </div>

                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Category
                            </button>

                            <a href="{{ route('admin.categories.index') }}"
                               class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection

