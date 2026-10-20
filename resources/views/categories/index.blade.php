@extends('layouts.storefront')

@section('title', 'Shop by Category')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge text-bg-primary rounded-pill px-3 py-2">Explore IBL CART 24</span>
        <h1 class="display-6 fw-bold mt-3 mb-2">Shop by Category</h1>
        <p class="text-secondary mb-0">Find products faster by browsing our collections.</p>
    </div>

    <form method="GET" action="{{ route('categories.index') }}" class="row g-2 justify-content-center mb-5">
        <div class="col-md-7 col-lg-6">
            <label class="visually-hidden" for="category-search">Search categories</label>
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input id="category-search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search categories...">
                <button class="btn btn-dark px-4" type="submit">Search</button>
            </div>
        </div>
        @if($search !== '')
            <div class="col-12 text-center"><a href="{{ route('categories.index') }}" class="small text-secondary">Clear search</a></div>
        @endif
    </form>

    @if($categories->count())
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('categories.show', $category) }}" class="text-decoration-none text-dark">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden category-card">
                            <div class="p-4 text-center bg-light">
                                <div class="mx-auto rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                                    <i class="fa-solid fa-layer-group fs-3 text-primary"></i>
                                </div>
                            </div>
                            <div class="card-body text-center p-4">
                                <h2 class="h5 fw-bold mb-2">{{ $category->name }}</h2>
                                <span class="badge rounded-pill text-bg-light border">
                                    {{ $category->products_count }} {{ $category->products_count == 1 ? 'product' : 'products' }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-5 d-flex justify-content-center">{{ $categories->links() }}</div>
    @else
        <div class="text-center bg-white rounded-4 shadow-sm p-5">
            <i class="fa-regular fa-folder-open fs-1 text-secondary"></i>
            <h2 class="h4 fw-bold mt-3">No categories found</h2>
            <p class="text-secondary mb-0">Try another search or check back later.</p>
        </div>
    @endif
</div>

<style>
.category-card{transition:transform .2s ease,box-shadow .2s ease}.category-card:hover{transform:translateY(-4px);box-shadow:0 18px 40px rgba(15,23,42,.10)!important}
</style>
@endsection