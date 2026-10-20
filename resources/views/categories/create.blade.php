@extends('layouts.admin')

@section('content')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-folder-plus me-2"></i>
                Add New Category
            </h2>

            <p class="text-muted mb-0">
                Create a new product category for your store.
            </p>
        </div>

        <a
            href="{{ route('admin.categories.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Categories
        </a>

    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="alert alert-danger shadow-sm">

            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Please fix the following errors:
            </div>

            <ul class="mb-0 ps-4">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Success Message --}}
    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show shadow-sm">

            <i class="bi bi-check-circle-fill me-1"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>

        </div>

    @endif


    {{-- Category Form --}}
    <div class="row justify-content-center">

        <div class="col-lg-8 col-xl-7">

            <div class="card border-0 shadow-sm">

                {{-- Card Header --}}
                <div class="card-header bg-dark text-white py-3">

                    <h5 class="mb-0">
                        <i class="bi bi-folder2-open me-2"></i>
                        Category Information
                    </h5>

                </div>


                {{-- Card Body --}}
                <div class="card-body p-4">

                    <form
                        action="{{ route('admin.categories.store') }}"
                        method="POST"
                    >

                        @csrf


                        {{-- Category Name --}}
                        <div class="mb-4">

                            <label
                                for="name"
                                class="form-label fw-semibold"
                            >

                                Category Name
                                <span class="text-danger">*</span>

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-tag"></i>
                                </span>

                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter category name"
                                    maxlength="255"
                                    autocomplete="off"
                                    required
                                    autofocus
                                >

                                @error('name')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>

                            <div class="form-text">
                                Example: Jewellery, Fashion, Accessories, Gift Items
                            </div>

                        </div>


                        {{-- Category Preview --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Preview
                            </label>

                            <div
                                id="categoryPreview"
                                class="border rounded-3 p-3 bg-light"
                            >

                                <div class="d-flex align-items-center gap-3">

                                    <div
                                        class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;"
                                    >
                                        <i class="bi bi-folder2-open fs-4 text-primary"></i>
                                    </div>

                                    <div>

                                        <div
                                            id="previewName"
                                            class="fw-semibold"
                                        >
                                            Category Name
                                        </div>

                                        <small class="text-muted">
                                            New product category
                                        </small>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Buttons --}}
                        <div class="d-flex flex-wrap justify-content-end gap-2">

                            <a
                                href="{{ route('admin.categories.index') }}"
                                class="btn btn-outline-secondary"
                            >

                                <i class="bi bi-x-circle me-1"></i>
                                Cancel

                            </a>


                            <button
                                type="reset"
                                class="btn btn-outline-warning"
                                id="resetButton"
                            >

                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset

                            </button>


                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                            >

                                <i class="bi bi-check-circle me-1"></i>
                                Create Category

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- Category Preview Script --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const nameInput = document.getElementById('name');
    const previewName = document.getElementById('previewName');
    const resetButton = document.getElementById('resetButton');

    if (nameInput && previewName) {

        nameInput.addEventListener('input', function () {

            const value = this.value.trim();

            previewName.textContent =
                value !== ''
                    ? value
                    : 'Category Name';

        });

    }


    if (resetButton) {

        resetButton.addEventListener('click', function () {

            setTimeout(function () {

                if (previewName) {
                    previewName.textContent = 'Category Name';
                }

            }, 10);

        });

    }

});

</script>

@endsection