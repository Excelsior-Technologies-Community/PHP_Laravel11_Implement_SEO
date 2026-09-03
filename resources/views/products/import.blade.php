@extends('products.layout')
@section('content')
<div class="container mt-4">
    <h2>Import Products (CSV)</h2>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <p>CSV columns (header row required):</p>
    <pre>product_name, price, size, color, description, focus_keyword, status, tags</pre>

    <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <input type="file" name="file" class="form-control" accept=".csv" required>
        </div>
        <button class="btn btn-primary">Import</button>
        <a href="{{ route('products.export') }}" class="btn btn-secondary">Download sample/export</a>
    </form>
</div>
@endsection
