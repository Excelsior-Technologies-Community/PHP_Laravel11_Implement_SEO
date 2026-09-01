@extends('products.layout')
@section('content')
<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h2>Products</h2>
        <div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
            <a href="{{ route('products.index', ['trash' => 1]) }}" class="btn btn-outline-secondary">Trash</a>
        </div>
    </div>

    <!-- Search / Filter / Sort -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name / description">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-control">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-control">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price Low</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price High</option>
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-dark w-100">Go</button>
        </div>
    </form>

    <!-- Bulk Action Form -->
    <form method="POST" action="{{ route('products.bulk') }}" id="bulkForm">
        @csrf

        <div class="mb-2">
            <button type="submit" name="action" value="active" class="btn btn-sm btn-success">Activate</button>
            <button type="submit" name="action" value="inactive" class="btn btn-sm btn-warning">Deactivate</button>
            <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
                    onclick="return confirm('Move selected to trash?')">Delete</button>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>#ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php $deleted = $product->trashed(); @endphp
                    <tr @if($deleted) class="table-secondary" @endif>
                        <td>
                            @unless($deleted)
                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="rowCheck">
                            @endunless
                        </td>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('images/' . $product->image) }}" width="60">
                            @else
                                <span>No Image</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product) }}">{{ $product->product_name }}</a>
                            <br><small class="text-muted">/{{ $product->slug }}</small>
                        </td>
                        <td>{{ $product->category?->name ?? '-' }}</td>
                        <td>
                            @foreach($product->tags as $tag)
                                <span class="badge bg-info text-dark">{{ $tag->name }}</span>
                            @endforeach
                        </td>
                        <td>{{ $product->price }}</td>
                        <td>
                            @if($product->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product) }}"
                               class="btn btn-info btn-sm text-white">Show</a>
                            <a href="{{ route('products.edit', $product) }}"
                               class="btn btn-warning btn-sm">Edit</a>

                            @if($deleted)
                                <a href="{{ route('products.restore', $product->id) }}"
                                   class="btn btn-success btn-sm">Restore</a>
                                <form action="{{ route('products.forceDelete', $product->id) }}"
                                      method="POST" style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Permanently delete?')">Purge</button>
                                </form>
                            @else
                                <form action="{{ route('products.destroy', $product) }}"
                                      method="POST" style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Move to trash?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No products available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    {{ $products->links() }}
</div>

@section('scripts')
<script>
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('.rowCheck').forEach(c => c.checked = this.checked);
    });
</script>
@endsection
@endsection
