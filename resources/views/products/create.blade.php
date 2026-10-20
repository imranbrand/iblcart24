@extends('layouts.admin')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-plus-circle me-2"></i>
                Add New Product
            </h2>

            <p class="text-muted mb-0">
                Create a new product for your store.
            </p>
        </div>

        <a
            href="{{ route('admin.products.index') }}"
            class="btn btn-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Products
        </a>

    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="alert alert-danger shadow-sm">

            <strong>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Please fix the following errors:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Product Form --}}
    <form
        action="{{ route('admin.products.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        <div class="card shadow-sm border-0">

            <div class="card-header bg-dark text-white py-3">

                <h5 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    Product Information
                </h5>

            </div>


            <div class="card-body p-4">

                <div class="row g-3">


                    {{-- Category --}}
                    <div class="col-md-6">

                        <label
                            for="category_id"
                            class="form-label fw-semibold"
                        >
                            Category
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="category_id"
                            id="category_id"
                            class="form-select @error('category_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Select Category --
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('category_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Product Name --}}
                    <div class="col-md-6">

                        <label
                            for="name"
                            class="form-label fw-semibold"
                        >
                            Product Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Enter product name"
                            maxlength="255"
                            required
                        >

                        @error('name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Price --}}
                    <div class="col-md-6">

                        <label
                            for="price"
                            class="form-label fw-semibold"
                        >
                            Price (৳)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                ৳
                            </span>

                            <input
                                type="number"
                                name="price"
                                id="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price') }}"
                                placeholder="Enter price"
                                min="0"
                                max="99999999.99"
                                step="0.01"
                                required
                            >

                            @error('price')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    {{-- Regular price (for Sale badge) --}}
                    <div class="col-md-6">

                        <label
                            for="compare_price"
                            class="form-label fw-semibold"
                        >
                            Regular price (৳)
                            <span class="text-muted fw-normal">- optional, shows a Sale badge</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                ৳
                            </span>

                            <input
                                type="number"
                                name="compare_price"
                                id="compare_price"
                                class="form-control @error('compare_price') is-invalid @enderror"
                                value="{{ old('compare_price') }}"
                                placeholder="Higher than the price above"
                                min="0"
                                max="99999999.99"
                                step="0.01"
                            >

                            @error('compare_price')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    {{-- Stock --}}
                    <div class="col-md-6">

                        <label
                            for="stock"
                            class="form-label fw-semibold"
                        >
                            Stock Quantity
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="stock"
                            id="stock"
                            class="form-control @error('stock') is-invalid @enderror"
                            value="{{ old('stock', 0) }}"
                            placeholder="Enter stock quantity"
                            min="0"
                            max="999999"
                            step="1"
                            required
                        >

                        @error('stock')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Product Image --}}
                    <div class="col-md-6">

                        <label
                            for="image"
                            class="form-label fw-semibold"
                        >
                            Product Image
                        </label>

                        <input
                            type="file"
                            name="image"
                            id="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="previewImage(event)"
                        >

                        <div class="form-text">
                            JPG, JPEG, PNG or WEBP. Maximum 5MB.
                        </div>

                        @error('image')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>



                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Gallery Images (optional, up to 8)</label>
                        <input type="file" name="gallery[]" class="form-control @error('gallery.*') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/jpg,image/webp" multiple>
                        @error('gallery.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Image Preview --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Image Preview
                        </label>

                        <div
                            class="border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                            style="width: 200px; height: 200px;"
                        >

                            <img
                                id="preview"
                                src=""
                                alt="Image Preview"
                                style="
                                    width: 180px;
                                    height: 180px;
                                    object-fit: cover;
                                    display: none;
                                    border-radius: 8px;
                                "
                            >

                            <div
                                id="previewPlaceholder"
                                class="text-muted text-center"
                            >

                                <i class="bi bi-image fs-1 d-block"></i>

                                <small>
                                    Image Preview
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- Description --}}
                    <div class="col-12">

                        <label
                            for="description"
                            class="form-label fw-semibold"
                        >
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="6"
                            maxlength="5000"
                            placeholder="Enter product description..."
                        >{{ old('description') }}</textarea>

                        <div class="form-text">
                            Maximum 5000 characters.
                        </div>

                        @error('description')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>


            <div class="card-footer bg-white py-3">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </a>

                    <button
                        type="reset"
                        class="btn btn-outline-warning"
                        onclick="resetPreview()"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >
                        <i class="bi bi-check-circle me-1"></i>
                        Save Product
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


<script>

function previewImage(event) {

    const file = event.target.files[0];

    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('previewPlaceholder');

    if (!file) {

        resetPreview();

        return;
    }

    if (!file.type.startsWith('image/')) {

        resetPreview();

        return;
    }

    const reader = new FileReader();

    reader.onload = function(e) {

        preview.src = e.target.result;

        preview.style.display = 'block';

        placeholder.style.display = 'none';

    };

    reader.readAsDataURL(file);
}


function resetPreview() {

    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('previewPlaceholder');
    const imageInput = document.getElementById('image');

    preview.src = '';

    preview.style.display = 'none';

    placeholder.style.display = 'block';

    if (imageInput) {
        imageInput.value = '';
    }
}

</script>

@endsection