@extends('products.layout')

@section('content')

<style>
    .products-page {
        background: #f6f8fb;
        min-height: 100vh;
        padding: 28px 0 50px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 5px;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 14px;
    }

    .modern-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .05);
    }

    .stat-card {
        padding: 20px;
        height: 100%;
        transition: .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(15, 23, 42, .08);
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 14px;
        background: #f3f4f6;
        color: #374151;
    }

    .stat-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 23px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .filter-card {
        padding: 20px;
        margin-top: 24px;
    }

    .filter-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
    }

    .form-control,
    .form-select {
        border: 1px solid #dfe3e8;
        border-radius: 10px;
        min-height: 42px;
        font-size: 14px;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #111827;
    }

    .btn-modern {
        border-radius: 10px;
        min-height: 42px;
        font-weight: 600;
        font-size: 14px;
        padding: 8px 16px;
    }

    .table-card {
        margin-top: 24px;
        overflow: hidden;
    }

    .table-header {
        padding: 18px 20px;
        border-bottom: 1px solid #edf0f3;
        background: #fff;
    }

    .table-title {
        font-size: 17px;
        font-weight: 700;
        margin: 0;
        color: #111827;
    }

    .table-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 3px;
    }

    .modern-table {
        margin: 0;
        vertical-align: middle;
    }

    .modern-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
        padding: 14px 15px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .modern-table tbody td {
        padding: 15px;
        border-color: #eef1f4;
        font-size: 14px;
        color: #374151;
    }

    .modern-table tbody tr {
        transition: .15s ease;
    }

    .modern-table tbody tr:hover {
        background: #fafbfc;
    }

    .product-image {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .no-image {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 11px;
        text-align: center;
    }

    .product-name {
        color: #111827;
        font-weight: 600;
        text-decoration: none;
    }

    .product-name:hover {
        color: #2563eb;
    }

    .product-slug {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        color: #94a3b8;
    }

    .price {
        font-weight: 700;
        color: #111827;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-active {
        background: #ecfdf3;
        color: #15803d;
    }

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
    }

    .tag-badge {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        border-radius: 6px;
        padding: 4px 7px;
        margin: 2px;
        font-size: 10px;
        font-weight: 600;
    }

    .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .action-btn {
        border-radius: 7px;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 9px;
    }

    .bulk-bar {
        padding: 15px 20px;
        background: #f8fafc;
        border-top: 1px solid #edf0f3;
        border-bottom: 1px solid #edf0f3;
    }

    .bulk-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-right: 8px;
    }

    .empty-state {
        padding: 60px 20px !important;
        text-align: center;
        color: #94a3b8 !important;
    }

    .empty-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: #f1f5f9;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
    }

    /* Number-only pagination */
    .pagination-wrapper {
        padding: 18px 20px;
        border-top: 1px solid #edf0f3;
        display: flex;
        justify-content: center;
        background: #fff;
    }

    .pagination {
        margin: 0;
        gap: 5px;
    }

    .pagination .page-link {
        border: 1px solid #e5e7eb;
        border-radius: 8px !important;
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        background: #fff;
        box-shadow: none;
    }

    .pagination .page-link:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .pagination .page-item.active .page-link {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background: #f8fafc;
    }

    @media (max-width: 768px) {
        .products-page {
            padding: 18px 0 30px;
        }

        .page-title {
            font-size: 23px;
        }

        .header-actions {
            width: 100%;
        }

        .header-actions .btn {
            flex: 1;
        }
    }
</style>


<div class="products-page">

    <div class="container-fluid px-3 px-md-4">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4"
                 role="alert">
                <strong>Success!</strong> {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif


        {{-- ==========================================================
             PAGE HEADER
        =========================================================== --}}

        <div class="d-flex flex-column flex-md-row justify-content-between
                    align-items-md-center gap-3 mb-4">

            <div>
                <h1 class="page-title">Products</h1>
                <div class="page-subtitle">
                    Manage your products, pricing, categories and status.
                </div>
            </div>

            <div class="header-actions d-flex gap-2">

                @if(request()->boolean('trash'))

                    <a href="{{ route('products.index') }}"
                       class="btn btn-outline-dark btn-modern">
                        ← Products
                    </a>

                @else

                    <a href="{{ route('products.index', ['trash' => 1]) }}"
                       class="btn btn-outline-secondary btn-modern">
                        🗑 Trash
                        @if(($trashedProducts ?? 0) > 0)
                            <span class="badge bg-danger ms-1">
                                {{ $trashedProducts }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('products.create') }}"
                       class="btn btn-dark btn-modern">
                        + Add Product
                    </a>

                @endif

            </div>

        </div>


        {{-- ==========================================================
             STATISTICS
        =========================================================== --}}

        @unless(request()->boolean('trash'))

            <div class="row g-3">

                {{-- Total --}}
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="modern-card stat-card">
                        <div class="stat-icon">#</div>

                        <div class="stat-label">
                            Total Products
                        </div>

                        <h3 class="stat-value">
                            {{ $totalProducts ?? 0 }}
                        </h3>
                    </div>
                </div>


                {{-- Active --}}
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="modern-card stat-card">
                        <div class="stat-icon">✓</div>

                        <div class="stat-label">
                            Active
                        </div>

                        <h3 class="stat-value text-success">
                            {{ $activeProducts ?? 0 }}
                        </h3>
                    </div>
                </div>


                {{-- Inactive --}}
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="modern-card stat-card">
                        <div class="stat-icon">!</div>

                        <div class="stat-label">
                            Inactive
                        </div>

                        <h3 class="stat-value text-danger">
                            {{ $inactiveProducts ?? 0 }}
                        </h3>
                    </div>
                </div>


                {{-- Average --}}
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="modern-card stat-card">
                        <div class="stat-icon">₹</div>

                        <div class="stat-label">
                            Average Price
                        </div>

                        <h3 class="stat-value">
                            ₹{{ number_format($averagePrice ?? 0, 2) }}
                        </h3>
                    </div>
                </div>


                {{-- Highest --}}
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="modern-card stat-card">
                        <div class="stat-icon">↑</div>

                        <div class="stat-label">
                            Highest Price
                        </div>

                        <h3 class="stat-value">
                            ₹{{ number_format($highestPrice ?? 0, 2) }}
                        </h3>
                    </div>
                </div>


                {{-- Lowest --}}
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="modern-card stat-card">
                        <div class="stat-icon">↓</div>

                        <div class="stat-label">
                            Lowest Price
                        </div>

                        <h3 class="stat-value">
                            ₹{{ number_format($lowestPrice ?? 0, 2) }}
                        </h3>
                    </div>
                </div>

            </div>

        @endunless


        {{-- ==========================================================
             FILTERS
        =========================================================== --}}

        <div class="modern-card filter-card">

            <div class="filter-title">
                Search & Filters
            </div>

            <form method="GET"
                  action="{{ route('products.index') }}">

                @if(request()->boolean('trash'))
                    <input type="hidden" name="trash" value="1">
                @endif

                <div class="row g-2">

                    {{-- Search --}}
                    <div class="col-12 col-lg-3">

                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               class="form-control"
                               placeholder="Search products...">

                    </div>


                    {{-- Category --}}
                    <div class="col-6 col-lg-2">

                        <select name="category_id"
                                class="form-select">

                            <option value="">
                                All Categories
                            </option>

                            @foreach($categories as $cat)

                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>

                                    {{ $cat->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Tag --}}
                    <div class="col-6 col-lg-2">

                        <select name="tag_id"
                                class="form-select">

                            <option value="">
                                All Tags
                            </option>

                            @foreach($tags as $tag)

                                <option value="{{ $tag->id }}"
                                    {{ request('tag_id') == $tag->id ? 'selected' : '' }}>

                                    {{ $tag->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}
                    <div class="col-6 col-lg-2">

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option value="1"
                                {{ request('status') === '1' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ request('status') === '0' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- Sort --}}
                    <div class="col-6 col-lg-2">

                        <select name="sort"
                                class="form-select">

                            <option value="latest"
                                {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>
                                Newest
                            </option>

                            <option value="oldest"
                                {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                                Oldest
                            </option>

                            <option value="name_asc"
                                {{ request('sort') === 'name_asc' ? 'selected' : '' }}>
                                Name A-Z
                            </option>

                            <option value="name_desc"
                                {{ request('sort') === 'name_desc' ? 'selected' : '' }}>
                                Name Z-A
                            </option>

                            <option value="price_asc"
                                {{ request('sort') === 'price_asc' ? 'selected' : '' }}>
                                Price Low
                            </option>

                            <option value="price_desc"
                                {{ request('sort') === 'price_desc' ? 'selected' : '' }}>
                                Price High
                            </option>

                        </select>

                    </div>


                    {{-- Search Button --}}
                    <div class="col-6 col-lg-1">

                        <button type="submit"
                                class="btn btn-dark btn-modern w-100">
                            Go
                        </button>

                    </div>

                </div>


                {{-- Price Filter --}}
                <div class="row g-2 mt-1">

                    <div class="col-6 col-lg-3">

                        <input type="number"
                               name="min_price"
                               value="{{ request('min_price') }}"
                               class="form-control"
                               placeholder="Min price"
                               min="0"
                               step="0.01">

                    </div>

                    <div class="col-6 col-lg-3">

                        <input type="number"
                               name="max_price"
                               value="{{ request('max_price') }}"
                               class="form-control"
                               placeholder="Max price"
                               min="0"
                               step="0.01">

                    </div>

                    <div class="col-6 col-lg-2">

                        <button type="submit"
                                class="btn btn-outline-dark btn-modern w-100">
                            Price Filter
                        </button>

                    </div>

                    <div class="col-6 col-lg-2">

                        <a href="{{ route('products.index') }}"
                           class="btn btn-outline-secondary btn-modern w-100">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>


        {{-- ==========================================================
             TRASH ACTIONS
        =========================================================== --}}

        @if(request()->boolean('trash'))

            <div class="modern-card p-3 mt-3">

                <form method="POST"
                      action="{{ route('products.bulkRestore') }}"
                      class="d-inline">

                    @csrf

                    <button type="submit"
                            class="btn btn-success btn-modern"
                            onclick="return confirm('Restore selected products?')">

                        Restore Selected

                    </button>

                </form>


                <form method="POST"
                      action="{{ route('products.emptyTrash') }}"
                      class="d-inline ms-2">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-outline-danger btn-modern"
                            onclick="return confirm('Permanently delete ALL trash products?')">

                        Empty Trash

                    </button>

                </form>

            </div>

        @endif


        {{-- ==========================================================
             PRODUCT TABLE
        =========================================================== --}}

        <div class="modern-card table-card">

            <div class="table-header">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h3 class="table-title">
                            {{ request()->boolean('trash') ? 'Trash Products' : 'All Products' }}
                        </h3>

                        <div class="table-subtitle">
                            {{ $products->total() }} product(s) found
                        </div>
                    </div>

                </div>

            </div>


            {{-- BULK ACTIONS --}}

            @unless(request()->boolean('trash'))

                <div class="bulk-bar">

                    <form method="POST"
                          action="{{ route('products.bulk') }}"
                          id="bulkForm">

                        @csrf

                        <span class="bulk-label">
                            Bulk Actions:
                        </span>

                        <button type="submit"
                                name="action"
                                value="active"
                                class="btn btn-success btn-sm action-btn">
                            Activate
                        </button>

                        <button type="submit"
                                name="action"
                                value="inactive"
                                class="btn btn-warning btn-sm action-btn">
                            Deactivate
                        </button>

                        <button type="submit"
                                name="action"
                                value="delete"
                                class="btn btn-danger btn-sm action-btn"
                                onclick="return confirm('Move selected products to trash?')">
                            Delete
                        </button>

                    </form>

                </div>

            @endunless


            <div class="table-responsive">

                <table class="table modern-table">

                    <thead>

                        <tr>

                            <th width="45">
                                <input type="checkbox"
                                       id="checkAll"
                                       class="form-check-input">
                            </th>

                            <th>ID</th>

                            <th>Product</th>

                            <th>Category</th>

                            <th>Tags</th>

                            <th>Price</th>

                            <th>Status</th>

                            <th width="270">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($products as $product)

                            @php
                                $deleted = $product->trashed();
                            @endphp

                            <tr class="{{ $deleted ? 'table-secondary' : '' }}">

                                {{-- Checkbox --}}
                                <td>

                                    @if($deleted)

                                        <span class="text-muted">—</span>

                                    @else

                                        <input type="checkbox"
                                               name="ids[]"
                                               value="{{ $product->id }}"
                                               class="form-check-input rowCheck"
                                               form="bulkForm">

                                    @endif

                                </td>


                                {{-- ID --}}
                                <td>

                                    <span class="fw-semibold">
                                        #{{ $product->id }}
                                    </span>

                                </td>


                                {{-- Product --}}
                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        @if($product->image)

                                            <img src="{{ asset('images/' . $product->image) }}"
                                                 class="product-image"
                                                 alt="{{ $product->product_name }}">

                                        @else

                                            <div class="no-image">
                                                No Image
                                            </div>

                                        @endif


                                        <div>

                                            <a href="{{ route('products.show', $product) }}"
                                               class="product-name">

                                                {{ $product->product_name }}

                                            </a>

                                            <span class="product-slug">
                                                /{{ $product->slug }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- Category --}}
                                <td>

                                    @if($product->category)

                                        <span class="fw-semibold">
                                            {{ $product->category->name }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Tags --}}
                                <td>

                                    @forelse($product->tags as $tag)

                                        <span class="tag-badge">
                                            {{ $tag->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted small">
                                            No tags
                                        </span>

                                    @endforelse

                                </td>


                                {{-- Price --}}
                                <td>

                                    <span class="price">
                                        ₹{{ number_format($product->price, 2) }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($product->status)

                                        <span class="status-badge status-active">
                                            ● Active
                                        </span>

                                    @else

                                        <span class="status-badge status-inactive">
                                            ● Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td>

                                    <div class="action-group">

                                        @if(!$deleted)

                                            <a href="{{ route('products.show', $product) }}"
                                               class="btn btn-outline-info action-btn">
                                                View
                                            </a>


                                            <a href="{{ route('products.edit', $product) }}"
                                               class="btn btn-outline-warning action-btn">
                                                Edit
                                            </a>


                                            <form action="{{ route('products.toggleStatus', $product) }}"
                                                  method="POST"
                                                  class="d-inline">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="btn btn-outline-secondary action-btn">
                                                    Toggle
                                                </button>

                                            </form>


                                            <form action="{{ route('products.duplicate', $product) }}"
                                                  method="POST"
                                                  class="d-inline">

                                                @csrf

                                                <button type="submit"
                                                        class="btn btn-outline-primary action-btn"
                                                        onclick="return confirm('Duplicate this product?')">

                                                    Duplicate

                                                </button>

                                            </form>


                                            <form action="{{ route('products.destroy', $product) }}"
                                                  method="POST"
                                                  class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-outline-danger action-btn"
                                                        onclick="return confirm('Move this product to trash?')">

                                                    Delete

                                                </button>

                                            </form>

                                        @else

                                            <a href="{{ route('products.restore', $product->id) }}"
                                               class="btn btn-outline-success action-btn">

                                                Restore

                                            </a>


                                            <form action="{{ route('products.forceDelete', $product->id) }}"
                                                  method="POST"
                                                  class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-outline-danger action-btn"
                                                        onclick="return confirm('Permanently delete this product?')">

                                                    Purge

                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="empty-state">

                                    <div class="empty-icon">
                                        📦
                                    </div>

                                    <div class="fw-semibold text-dark mb-1">
                                        No products found
                                    </div>

                                    <div class="small">
                                        Try changing your search or filters.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ======================================================
                 NUMBER ONLY PAGINATION
            ======================================================= --}}

            @if($products->hasPages())

                <div class="pagination-wrapper">

                    <nav aria-label="Products pagination">

                        <ul class="pagination">

                            {{-- Previous --}}
                            @if($products->onFirstPage())

                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>

                            @else

                                <li class="page-item">
                                    <a class="page-link"
                                       href="{{ $products->previousPageUrl() }}">
                                        ‹
                                    </a>
                                </li>

                            @endif


                            {{-- Page Numbers --}}
                            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)

                                <li class="page-item {{ $page == $products->currentPage() ? 'active' : '' }}">

                                    <a class="page-link"
                                       href="{{ $url }}">
                                        {{ $page }}
                                    </a>

                                </li>

                            @endforeach


                            {{-- Next --}}
                            @if($products->hasMorePages())

                                <li class="page-item">
                                    <a class="page-link"
                                       href="{{ $products->nextPageUrl() }}">
                                        ›
                                    </a>
                                </li>

                            @else

                                <li class="page-item disabled">
                                    <span class="page-link">›</span>
                                </li>

                            @endif

                        </ul>

                    </nav>

                </div>

            @endif

        </div>

    </div>

</div>


@endsection


@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const checkAll = document.getElementById('checkAll');

    if (checkAll) {

        checkAll.addEventListener('change', function () {

            document
                .querySelectorAll('.rowCheck')
                .forEach(function (checkbox) {

                    checkbox.checked = checkAll.checked;

                });

        });

    }

});

</script>

@endsection