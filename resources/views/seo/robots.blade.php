@extends('products.layout')
@section('content')
<div class="container mt-4">
    <h2>Manage robots.txt</h2>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <form action="{{ route('robots.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>robots.txt Content</label>
            <textarea name="content" class="form-control" rows="15">{{ $content }}</textarea>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-secondary">View live</a>
    </form>
</div>
@endsection
