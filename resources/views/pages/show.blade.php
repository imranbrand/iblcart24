@extends('layouts.storefront')

@section('title', $title)

@section('content')
<div class="container py-5" style="max-width: 900px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    <h1 class="h2 fw-bold mb-4">{{ $title }}</h1>

    <div class="card border-0 shadow-sm" style="border-radius:20px;">
        <div class="card-body p-4 p-md-5" style="line-height:1.8;">
            @include('pages.content.' . $slug)
        </div>
    </div>

</div>
@endsection
