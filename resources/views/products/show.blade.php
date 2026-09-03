@php
    use Illuminate\Support\Str;
@endphp


@extends('products.layout')

@section('title', $product->seo_meta_title ?? $product->product_name)

@section('meta')
<!-- SEO META -->
<meta name="title"
      content="{{ $product->seo_meta_title ?: $product->product_name }}">

<meta name="description"
      content="{{ $product->seo_meta_description ?: Str::limit(strip_tags($product->description), 160) }}">

<meta name="keywords"
      content="{{ $product->seo_meta_key ?: $product->focus_keyword }}">

@if($product->seo_canonical)
<link rel="canonical" href="{{ $product->seo_canonical }}">
@endif

<!-- OPEN GRAPH -->
<meta property="og:title" content="{{ $product->og_meta_title ?: $product->product_name }}">
<meta property="og:description" content="{{ $product->og_meta_description ?: $product->seo_meta_description }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ config('seo.default_locale') }}">
@if($product->og_meta_image)
<meta property="og:image" content="{{ asset('images/' . $product->og_meta_image) }}">
@endif

<!-- TWITTER CARD -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $product->og_meta_title ?: $product->product_name }}">
<meta name="twitter:description" content="{{ $product->og_meta_description ?: $product->seo_meta_description }}">
@if($product->og_meta_image)
<meta name="twitter:image" content="{{ asset('images/' . $product->og_meta_image) }}">
@endif

<!-- HREFLANG ALTERNATES -->
@foreach(config('seo.locales') as $code => $label)
<link rel="alternate" hreflang="{{ $code }}" href="{{ url('/products/show/' . $product->slug . '?lang=' . $code) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ url('/products/show/' . $product->slug) }}">

<!-- JSON-LD STRUCTURED DATA -->
<script type="application/ld+json">
    {
        !!json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!
    }
</script>
@endsection

@section('content')
<div class="container mt-4">
    <h2>Product Details</h2>
    <hr>

    <div class="row">
        <div class="col-md-4">
            @if($product->image)
            <img src="{{ asset('images/' . $product->image) }}" width="100%" class="img-thumbnail">
            @else
            <div class="alert alert-warning">No image available</div>
            @endif
        </div>
        <div class="col-md-8">
            <h3>{{ $product->product_name }}</h3>
            <p><strong>Price:</strong> ₹{{ $product->price }}</p>
            <p><strong>Size:</strong> {{ $product->size }}</p>
            <p><strong>Color:</strong> {{ $product->color }}</p>
            <p><strong>Category:</strong> {{ $product->category?->name ?? '-' }}</p>
            <p>
                <strong>Tags:</strong>
                @foreach($product->tags as $tag)
                <span class="badge bg-info text-dark">{{ $tag->name }}</span>
                @endforeach
            </p>
            <hr>
            <h5>Description</h5>
            <p>{!! $product->description !!}</p>

            <!-- SEO AUDIT -->
            <!-- SEO HEALTH SCORE -->
            @php
            $seoScore = \App\Traits\SeoAudit::seoScore($product);
            $seoStatus = \App\Traits\SeoAudit::seoScoreStatus($seoScore);

            $scoreClass = match(true) {
            $seoScore >= 90 => 'success',
            $seoScore >= 75 => 'primary',
            $seoScore >= 50 => 'warning',
            default => 'danger',
            };
            @endphp

            <hr>

            <div class="card mb-4 shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        SEO Health Score
                    </h5>

                    <span class="badge bg-{{ $scoreClass }}">
                        {{ $seoStatus }}
                    </span>
                </div>

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-md-4 text-center">

                            <div class="display-4 fw-bold text-{{ $scoreClass }}">
                                {{ $seoScore }}
                            </div>

                            <div class="text-muted">
                                SEO Score / 100
                            </div>

                        </div>

                        <div class="col-md-8">

                            <div class="progress mb-3" style="height: 20px;">

                                <div class="progress-bar bg-{{ $scoreClass }}"
                                    style="width: {{ $seoScore }}%">
                                    {{ $seoScore }}%
                                </div>

                            </div>

                            <p class="mb-0">
                                <strong>Focus Keyword:</strong>

                                @if($product->focus_keyword)

                                <span class="badge bg-info text-dark">
                                    {{ $product->focus_keyword }}
                                </span>

                                @else

                                <span class="text-danger">
                                    Not configured
                                </span>

                                @endif
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- SEO AUDIT -->
            <h5>SEO Audit</h5>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seoAudit as $check)
                    <tr>
                        <td>{{ $check['label'] }}</td>
                        <td>
                            @if($check['status'] === 'good')
                            <span class="badge bg-success">Good</span>
                            @elseif($check['status'] === 'warn')
                            <span class="badge bg-warning text-dark">Warning</span>
                            @else
                            <span class="badge bg-danger">Missing</span>
                            @endif
                        </td>
                        <td>{{ $check['message'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>
            <h5>SEO Meta Information</h5>
            <p><strong>SEO Title:</strong> {{ $product->seo_meta_title }}</p>
            <p><strong>SEO Keywords:</strong> {{ $product->seo_meta_key }}</p>
            <p><strong>SEO Description:</strong> {{ $product->seo_meta_description }}</p>
            <p><strong>Canonical URL:</strong> {{ $product->seo_canonical }}</p>
            @if($product->seo_meta_image)
            <p><strong>SEO Image:</strong></p>
            <img src="{{ asset('images/' . $product->seo_meta_image) }}" width="200">
            @endif

            <hr>
            <h5>Open Graph (OG) Meta Information</h5>
            <p><strong>OG Title:</strong> {{ $product->og_meta_title }}</p>
            <p><strong>OG Keywords:</strong> {{ $product->og_meta_key }}</p>
            <p><strong>OG Description:</strong> {{ $product->og_meta_description }}</p>
            @if($product->og_meta_image)
            <p><strong>OG Image:</strong></p>
            <img src="{{ asset('images/' . $product->og_meta_image) }}" width="200">
            @endif

            <hr>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection