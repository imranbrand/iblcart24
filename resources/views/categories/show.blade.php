<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $category->name }} - IBL CART 24
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .page-header {
            background: linear-gradient(
                135deg,
                #111827,
                #1f2937
            );

            color: white;

            border-radius: 15px;

            padding: 30px;

            margin-bottom: 30px;
        }

        .product-card {
            border: none;

            border-radius: 15px;

            overflow: hidden;

            transition: 0.3s;

            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.10);
        }

        .product-image {
            width: 100%;

            height: 220px;

            object-fit: cover;

            background: #f1f5f9;
        }

        .product-placeholder {
            width: 100%;

            height: 220px;

            background: #f1f5f9;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #94a3b8;

            font-size: 40px;
        }

        .price {
            font-size: 20px;

            font-weight: 700;

            color: #198754;
        }

        .stock {
            font-size: 13px;

            color: #6b7280;
        }

        .empty-box {
            background: white;

            border-radius: 15px;

            padding: 70px 20px;

            text-align: center;
        }

    </style>

</head>


<body>


<div class="container-fluid py-4">


    <!-- HEADER -->

    <div class="page-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <div class="mb-2">

                    <span class="badge bg-light text-dark">

                        <i class="fa-solid fa-layer-group me-1"></i>

                        Category

                    </span>

                </div>


                <h1 class="fw-bold mb-2">

                    {{ $category->name }}

                </h1>


                <p class="mb-0 text-white-50">

                    Browse all products in this category.

                </p>

            </div>


            <div class="mt-3 mt-md-0">

                <a
                    href="{{ route('categories.index') }}"
                    class="btn btn-light"
                >

                    <i class="fa-solid fa-arrow-left me-1"></i>

                    Back to Categories

                </a>

            </div>

        </div>

    </div>



    <!-- SUCCESS MESSAGE -->

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



    <!-- PRODUCTS -->

    @if($category->products->count() > 0)


        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold mb-1">

                    Products

                </h3>

                <p class="text-muted mb-0">

                    {{ $category->products->count() }}
                    product(s) found

                </p>

            </div>


            <!-- FIXED ADMIN PRODUCT CREATE ROUTE -->

            <a
                href="{{ route('admin.products.create') }}"
                class="btn btn-primary"
            >

                <i class="fa-solid fa-plus me-1"></i>

                Add Product

            </a>

        </div>



        <div class="row g-4">


            @foreach($category->products as $product)


                <div class="col-xl-3 col-lg-4 col-md-6">


                    <div class="card product-card shadow-sm">


                        <!-- PRODUCT IMAGE -->

                        @if($product->image)

                            <img
                                src="{{ asset('uploads/products/' . $product->image) }}"
                                class="product-image"
                                alt="{{ $product->name }}"
                            >

                        @else

                            <div class="product-placeholder">

                                <i class="fa-solid fa-image"></i>

                            </div>

                        @endif



                        <!-- PRODUCT BODY -->

                        <div class="card-body p-4">


                            <h5 class="fw-bold mb-2">

                                {{ $product->name }}

                            </h5>


                            @if($product->description)

                                <p class="text-muted small mb-3">

                                    {{ \Illuminate\Support\Str::limit(
                                        $product->description,
                                        90
                                    ) }}

                                </p>

                            @else

                                <p class="text-muted small mb-3">

                                    No description available.

                                </p>

                            @endif



                            <!-- PRICE -->

                            <div class="price mb-1">

                                ৳{{ number_format(
                                    $product->price,
                                    2
                                ) }}

                            </div>



                            <!-- STOCK -->

                            <div class="stock mb-3">

                                @if($product->stock > 0)

                                    <span class="text-success">

                                        <i class="fa-solid fa-circle-check me-1"></i>

                                        {{ $product->stock }}
                                        in stock

                                    </span>

                                @else

                                    <span class="text-danger">

                                        <i class="fa-solid fa-circle-xmark me-1"></i>

                                        Out of stock

                                    </span>

                                @endif

                            </div>



                            <!-- BUTTONS -->

                            <div class="d-flex gap-2">


                                <!-- VIEW PRODUCT -->

                                <a
                                    href="{{ route(
                                        'products.show',
                                        $product->id
                                    ) }}"
                                    class="btn btn-outline-primary flex-grow-1"
                                >

                                    <i class="fa-solid fa-eye me-1"></i>

                                    View

                                </a>



                                <!-- FIXED ADMIN EDIT ROUTE -->

                                <a
                                    href="{{ route(
                                        'admin.products.edit',
                                        $product->id
                                    ) }}"
                                    class="btn btn-outline-secondary"
                                    title="Edit Product"
                                >

                                    <i class="fa-solid fa-pen"></i>

                                </a>


                            </div>


                        </div>

                    </div>


                </div>


            @endforeach


        </div>


    @else


        <!-- EMPTY -->

        <div class="empty-box shadow-sm">


            <div class="mb-3">

                <i class="fa-solid fa-box-open fa-4x text-muted"></i>

            </div>


            <h3 class="fw-bold">

                No Products Found

            </h3>


            <p class="text-muted">

                There are currently no products in
                <strong>{{ $category->name }}</strong>.

            </p>


            <!-- FIXED ADMIN PRODUCT CREATE ROUTE -->

            <a
                href="{{ route('admin.products.create') }}"
                class="btn btn-primary"
            >

                <i class="fa-solid fa-plus me-1"></i>

                Add Product

            </a>


        </div>


    @endif



    <!-- FOOTER -->

    <div class="text-center mt-5 mb-3 text-muted">

        <strong>

            IBL CART 24

        </strong>

        <br>

        <small>

            © {{ date('Y') }}
            IBL CART 24.
            All Rights Reserved.

        </small>

    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>