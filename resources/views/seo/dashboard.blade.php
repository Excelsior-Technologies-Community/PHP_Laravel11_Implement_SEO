@extends('products.layout')

@section('title', 'SEO Dashboard')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>SEO Health Dashboard</h2>
            <p class="text-muted mb-0">
                Monitor product SEO quality and focus keyword optimization.
            </p>
        </div>

        <a href="{{ route('products.index') }}"
           class="btn btn-secondary">
            Products
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Total Products</small>
                    <h3 class="mt-2">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Average Score</small>
                    <h3 class="mt-2">{{ $averageScore }}%</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-success">Excellent</small>
                    <h3 class="mt-2 text-success">{{ $excellent }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-primary">Good</small>
                    <h3 class="mt-2 text-primary">{{ $good }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-warning">Needs Improvement</small>
                    <h3 class="mt-2 text-warning">{{ $needsImprovement }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-danger">Critical</small>
                    <h3 class="mt-2 text-danger">{{ $critical }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Search / Filter --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" class="row g-2">

                <div class="col-md-5">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Search product or focus keyword">
                </div>

                <div class="col-md-4">
                    <select name="score" class="form-control">

                        <option value="">
                            All SEO Scores
                        </option>

                        <option value="excellent"
                            {{ request('score') === 'excellent' ? 'selected' : '' }}>
                            Excellent (90-100)
                        </option>

                        <option value="good"
                            {{ request('score') === 'good' ? 'selected' : '' }}>
                            Good (75-89)
                        </option>

                        <option value="warning"
                            {{ request('score') === 'warning' ? 'selected' : '' }}>
                            Needs Improvement (50-74)
                        </option>

                        <option value="critical"
                            {{ request('score') === 'critical' ? 'selected' : '' }}>
                            Critical (0-49)
                        </option>

                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-dark">
                        Filter
                    </button>

                    <a href="{{ route('seo.dashboard') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>

            </form>

        </div>
    </div>

    {{-- SEO Products --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Product SEO Analysis</strong>
        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-striped mb-0">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Focus Keyword</th>
                        <th>SEO Score</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($rows as $row)

                    @php
                        $score = $row['score'];

                        if ($score >= 90) {
                            $badge = 'success';
                        } elseif ($score >= 75) {
                            $badge = 'primary';
                        } elseif ($score >= 50) {
                            $badge = 'warning';
                        } else {
                            $badge = 'danger';
                        }
                    @endphp

                    <tr>

                        <td>
                            {{ $row['product']->id }}
                        </td>

                        <td>

                            <a href="{{ route('products.show', $row['product']) }}">
                                {{ $row['product']->product_name }}
                            </a>

                            <br>

                            <small class="text-muted">
                                /{{ $row['product']->slug }}
                            </small>

                        </td>

                        <td>

                            @if($row['product']->focus_keyword)

                                <span class="badge bg-info text-dark">
                                    {{ $row['product']->focus_keyword }}
                                </span>

                            @else

                                <span class="text-danger">
                                    Not configured
                                </span>

                            @endif

                        </td>

                        <td style="min-width:180px">

                            <div class="d-flex align-items-center gap-2">

                                <strong>
                                    {{ $score }}/100
                                </strong>

                                <div class="progress flex-grow-1"
                                     style="height:8px;">

                                    <div class="progress-bar bg-{{ $badge }}"
                                         style="width: {{ $score }}%">
                                    </div>

                                </div>

                            </div>

                        </td>

                        <td>

                            <span class="badge bg-{{ $badge }}">
                                {{ $row['status'] }}
                            </span>

                        </td>

                        <td>

                            <a href="{{ route('products.show', $row['product']) }}"
                               class="btn btn-info btn-sm text-white">
                                Audit
                            </a>

                            <a href="{{ route('products.edit', $row['product']) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"
                            class="text-center py-4">

                            No products found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection