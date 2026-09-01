@extends('products.layout')
@section('content')
<div class="container mt-4">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="d-flex justify-content-between mb-3">
        <h2>Categories</h2>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr><th>#</th><th>Name</th><th>Slug</th><th>Status</th><th width="160">Actions</th></tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->slug }}</td>
                    <td>
                        @if($cat->status)<span class="badge bg-success">Active</span>
                        @else<span class="badge bg-danger">Inactive</span>@endif
                    </td>
                    <td>
                        <a href="{{ route('categories.edit', $cat) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No categories</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $categories->links() }}
</div>
@endsection
