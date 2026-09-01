@extends('products.layout')
@section('content')
<div class="container mt-4">
    <h2>Edit Product</h2>

    <form action="{{ route('products.update', $product) }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Live Meta Preview -->
        <div class="card mb-3">
            <div class="card-header">Live Meta Preview</div>
            <div class="card-body">
                <h6>Google Search Result</h6>
                <div id="googlePreview" class="border p-2 mb-3">
                    <div class="text-success" style="font-size:12px;">{{ url('/') }}/products/show/<span id="pvSlug">{{ $product->slug }}</span></div>
                    <div id="pvGoogleTitle" class="text-primary" style="font-size:18px; color:#1a0dab;">{{ $product->seo_meta_title ?: $product->product_name }}</div>
                    <div id="pvGoogleDesc" style="font-size:13px; color:#4d5156;">{{ $product->seo_meta_description }}</div>
                </div>
                <h6>Facebook / Open Graph Card</h6>
                <div id="fbPreview" class="border p-2" style="max-width:400px;">
                    <div id="pvFbImage" class="bg-light text-center mb-2" style="height:150px; line-height:150px; overflow:hidden;">
                        @if($product->og_meta_image)
                            <img src="{{ asset('images/' . $product->og_meta_image) }}" style="max-height:150px; max-width:100%;">
                        @else
                            No image
                        @endif
                    </div>
                    <div id="pvFbTitle" class="fw-bold">{{ $product->og_meta_title ?: $product->product_name }}</div>
                    <div id="pvFbDesc" style="font-size:13px; color:#606770;">{{ $product->og_meta_description }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Product Name *</label>
                <input type="text" name="product_name" id="product_name"
                       value="{{ $product->product_name }}" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>SEO Slug (auto)</label>
                <input type="text" id="slugPreview" value="{{ $product->slug }}" class="form-control" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Price *</label>
                <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Size</label>
                <input type="text" name="size" value="{{ $product->size }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Color</label>
                <input type="text" name="color" value="{{ $product->color }}" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>Tags (comma separated)</label>
                <input type="text" name="tags" value="{{ $productTags }}" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>Description</label>
                <div id="editor" style="height:200px;">{!! $product->description !!}</div>
                <textarea name="description" id="description" style="display:none;">{!! $product->description !!}</textarea>
            </div>
            <div class="col-md-12 mb-2">
                <label>Current Image:</label><br>
                @if($product->image)
                    <img src="{{ asset('images/' . $product->image) }}" width="100">
                @endif
            </div>
            <div class="col-md-12 mb-3">
                <label>Change Image</label>
                <input type="file" name="image" class="form-control" onchange="previewImage(this, 'previewProduct')">
                <img id="previewProduct" style="width:120px; display:none; margin-top:10px;">
            </div>
            <hr>

            <h4>SEO Meta Information</h4>
            <div class="col-md-6 mb-3">
                <label>SEO Title</label>
                <input type="text" name="seo_meta_title" id="seo_meta_title" value="{{ $product->seo_meta_title }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>SEO Keywords</label>
                <input type="text" name="seo_meta_key" value="{{ $product->seo_meta_key }}" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>SEO Description</label>
                <textarea name="seo_meta_description" id="seo_meta_description" class="form-control">{{ $product->seo_meta_description }}</textarea>
            </div>
            <div class="col-md-6 mb-2">
                <label>Current SEO Image:</label><br>
                @if($product->seo_meta_image)
                    <img src="{{ asset('images/' . $product->seo_meta_image) }}" width="100">
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label>Change SEO Image</label>
                <input type="file" name="seo_meta_image" class="form-control" onchange="previewImage(this, 'previewSeo')">
                <img id="previewSeo" style="width:120px; display:none; margin-top:10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label>Canonical URL</label>
                <input type="text" name="seo_canonical" value="{{ $product->seo_canonical }}" class="form-control">
            </div>
            <hr>

            <h4>Open Graph (OG) Meta Information</h4>
            <div class="col-md-6 mb-3">
                <label>OG Title</label>
                <input type="text" name="og_meta_title" id="og_meta_title" value="{{ $product->og_meta_title }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>OG Keywords</label>
                <input type="text" name="og_meta_key" value="{{ $product->og_meta_key }}" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>OG Description</label>
                <textarea name="og_meta_description" id="og_meta_description" class="form-control">{{ $product->og_meta_description }}</textarea>
            </div>
            <div class="col-md-6 mb-2">
                <label>Current OG Image:</label><br>
                @if($product->og_meta_image)
                    <img src="{{ asset('images/' . $product->og_meta_image) }}" width="100">
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label>Change OG Image</label>
                <input type="file" name="og_meta_image" class="form-control" onchange="ogPreview(this)">
                <img id="ogPreviewImg" style="width:120px; display:none; margin-top:10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <button class="btn btn-success mt-3">Update</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>

@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script>
    var quill = new Quill('#editor', { theme: 'snow' });
    quill.on('text-change', function () {
        document.getElementById('description').value = quill.root.innerHTML;
    });

    function previewImage(input, id) {
        let file = input.files[0];
        if (! file) return;
        let reader = new FileReader();
        reader.onload = e => {
            document.getElementById(id).src = e.target.result;
            document.getElementById(id).style.display = "block";
        };
        reader.readAsDataURL(file);
    }

    function ogPreview(input) {
        let file = input.files[0];
        if (! file) return;
        let reader = new FileReader();
        reader.onload = e => {
            document.getElementById('ogPreviewImg').src = e.target.result;
            document.getElementById('ogPreviewImg').style.display = "block";
            document.getElementById('pvFbImage').innerHTML =
                '<img src="' + e.target.result + '" style="max-height:150px; max-width:100%;">';
        };
        reader.readAsDataURL(file);
    }

    function slugify(text) {
        return text.toString().toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-');
    }

    document.getElementById('product_name').addEventListener('input', function () {
        let slug = slugify(this.value);
        document.getElementById('slugPreview').value = slug;
        document.getElementById('pvSlug').textContent = slug;
        if (! document.getElementById('seo_meta_title').value)
            document.getElementById('pvGoogleTitle').textContent = this.value || 'Product Title';
        if (! document.getElementById('og_meta_title').value)
            document.getElementById('pvFbTitle').textContent = this.value || 'OG Title';
    });

    document.getElementById('seo_meta_title').addEventListener('input', function () {
        document.getElementById('pvGoogleTitle').textContent = this.value || 'Product Title';
    });
    document.getElementById('seo_meta_description').addEventListener('input', function () {
        document.getElementById('pvGoogleDesc').textContent = this.value || 'Product description preview...';
    });
    document.getElementById('og_meta_title').addEventListener('input', function () {
        document.getElementById('pvFbTitle').textContent = this.value || 'OG Title';
    });
    document.getElementById('og_meta_description').addEventListener('input', function () {
        document.getElementById('pvFbDesc').textContent = this.value || 'OG description preview...';
    });
</script>
@endsection
@endsection
