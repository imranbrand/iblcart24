@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            ✏️ Edit Product
        </h2>

        <a
            href="{{ route('products.index') }}"
            class="btn btn-secondary"
        >
            ← Back to Products
        </a>

    </div>


    <!-- Success Message -->
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fa-solid fa-circle-check me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    <!-- Errors -->
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Please fix the following errors:</strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card shadow-sm">

        <div class="card-header bg-warning">

            <h5 class="mb-0 fw-bold">
                Product Information
            </h5>

        </div>


        <div class="card-body">

            <!-- IMPORTANT:
                 admin.products.update is the correct route
            -->

            <form
                action="{{ route('admin.products.update', $product->id) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                @method('PUT')


                <div class="row">


                    <!-- Category -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Category
                            <span class="text-danger">*</span>

                        </label>


                        <select
                            name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Select Category --
                            </option>


                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ old(
                                        'category_id',
                                        $product->category_id
                                    ) == $category->id ? 'selected' : '' }}
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



                    <!-- Product Name -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Product Name
                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $product->name) }}"
                            placeholder="Enter product name"
                            required
                        >


                        @error('name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>



                    <!-- Price -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Price (৳)
                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="number"
                            name="price"
                            class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price', $product->price) }}"
                            min="0"
                            step="0.01"
                            placeholder="Enter product price"
                            required
                        >


                        @error('price')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>



                    <!-- Regular price (for Sale badge) -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Regular price (৳)
                            <span class="text-muted fw-normal">- optional, shows a Sale badge</span>

                        </label>

                        <input
                            type="number"
                            name="compare_price"
                            class="form-control @error('compare_price') is-invalid @enderror"
                            value="{{ old('compare_price', $product->compare_price) }}"
                            min="0"
                            max="99999999.99"
                            step="0.01"
                            placeholder="Higher than the price above"
                        >

                        @error('compare_price')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- Stock -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Stock
                            <span class="text-danger">*</span>

                        </label>


                        <input
                            type="number"
                            name="stock"
                            class="form-control @error('stock') is-invalid @enderror"
                            value="{{ old('stock', $product->stock) }}"
                            min="0"
                            placeholder="Enter stock quantity"
                            required
                        >


                        @error('stock')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>



                    <!-- Current Image -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Current Image
                        </label>


                        <div class="mb-2">

                            @if($product->image)

                                <img
                                    src="{{ asset('uploads/products/' . $product->image) }}"
                                    alt="{{ $product->name }}"
                                    id="currentImage"
                                    class="img-thumbnail"
                                    style="
                                        width:180px;
                                        height:180px;
                                        object-fit:cover;
                                    "
                                >

                            @else

                                <div
                                    class="border rounded p-5 text-muted text-center"
                                    style="
                                        width:180px;
                                        height:180px;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                    "
                                >

                                    <div>

                                        <i class="fa-solid fa-image fa-2x mb-2"></i>

                                        <br>

                                        No Image

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>



                    <!-- New Image -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Change Image

                        </label>


                        <input
                            type="file"
                            name="image"
                            id="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="previewImage(event)"
                        >


                        <small class="text-muted">

                            Leave empty to keep the current image.

                        </small>


                        @error('image')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror



                        <!-- New Image Preview -->

                        <div class="mt-3">

                            <img
                                id="preview"
                                src=""
                                alt="New Image Preview"
                                class="img-thumbnail"
                                style="
                                    width:180px;
                                    height:180px;
                                    object-fit:cover;
                                    display:none;
                                "
                            >

                        </div>

                    </div>




                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Gallery Images (optional, up to 8)</label>
                        <input type="file" name="gallery[]" class="form-control @error('gallery.*') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/jpg,image/webp" multiple>
                        @error('gallery.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if($product->images->count())
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Current gallery (tick to remove)</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($product->images as $g)
                                    <label class="text-center small">
                                        <img src="{{ asset('uploads/products/' . $g->image) }}" class="img-thumbnail d-block"
                                             style="width:90px;height:90px;object-fit:cover;">
                                        <input type="checkbox" name="remove_gallery[]" value="{{ $g->id }}"> Remove
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="col-12 mb-3">

                        <label class="form-label fw-semibold">

                            Description

                        </label>


                        <textarea
                            name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="5"
                            placeholder="Enter product description"
                        >{{ old('description', $product->description) }}</textarea>


                        @error('description')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                </div>



                <!-- Buttons -->

                <div class="d-flex justify-content-end gap-2 mt-3">


                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-secondary"
                    >

                        <i class="fa-solid fa-arrow-left me-1"></i>

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="fa-solid fa-check me-1"></i>

                        Update Product

                    </button>


                </div>


            </form>

        </div>

    </div>

</div>



<!-- Image Preview -->

<script>

function previewImage(event) {

    const file = event.target.files[0];

    const preview = document.getElementById('preview');


    if (!file) {

        preview.style.display = 'none';

        preview.src = '';

        return;

    }


    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/webp'
    ];


    if (!allowedTypes.includes(file.type)) {

        alert('Please select a valid image file.');

        event.target.value = '';

        preview.style.display = 'none';

        preview.src = '';

        return;

    }


    const reader = new FileReader();


    reader.onload = function(e) {

        preview.src = e.target.result;

        preview.style.display = 'block';

    };


    reader.readAsDataURL(file);

}

</script>


@endsection

