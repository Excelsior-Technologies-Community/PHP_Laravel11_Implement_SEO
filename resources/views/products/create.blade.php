@extends('products.layout')
@section('content')
<div class="container mt-4">

    <h2>Add Product</h2>

    <form action="{{ route('products.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Live Meta Preview -->
        <div class="card mb-3">
            <div class="card-header">Live Meta Preview</div>
            <div class="card-body">
                <h6>Google Search Result</h6>
                <div id="googlePreview" class="border p-2 mb-3">
                    <div class="text-success" style="font-size:12px;">{{ url('/') }}/products/show/<span id="pvSlug">slug</span></div>
                    <div id="pvGoogleTitle" class="text-primary" style="font-size:18px; color:#1a0dab;">Product Title</div>
                    <div id="pvGoogleDesc" style="font-size:13px; color:#4d5156;">Product description preview...</div>
                </div>
                <h6>Facebook / Open Graph Card</h6>
                <div id="fbPreview" class="border p-2" style="max-width:400px;">
                    <div id="pvFbImage" class="bg-light text-center mb-2" style="height:150px; line-height:150px;">No image</div>
                    <div id="pvFbTitle" class="fw-bold">OG Title</div>
                    <div id="pvFbDesc" style="font-size:13px; color:#606770;">OG description preview...</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Product Name -->
            <div class="col-md-6 mb-3">
                <label>Product Name *</label>
                <input type="text" name="product_name" id="product_name" class="form-control" required>
            </div>
            <!-- Slug preview -->
            <div class="col-md-6 mb-3">
                <label>SEO Slug (auto)</label>
                <input type="text" id="slugPreview" class="form-control" readonly>
            </div>
            <!-- Price -->
            <div class="col-md-6 mb-3">
                <label>Price *</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <!-- Category -->
            <div class="col-md-6 mb-3">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Size / Color -->
            <div class="col-md-6 mb-3">
                <label>Size</label>
                <input type="text" name="size" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Color</label>
                <input type="text" name="color" class="form-control">
            </div>
            <!-- Tags -->
            <div class="col-md-12 mb-3">
                <label>Tags (comma separated)</label>
                <input type="text" name="tags" class="form-control" placeholder="red, summer, sale">
            </div>
            <!-- Description (Rich Text) -->
            <div class="col-md-12 mb-3">
                <label>Description</label>
                <div id="editor" style="height:200px;"></div>
                <textarea name="description" id="description" style="display:none;"></textarea>
            </div>
            <!-- Product Image -->
            <div class="col-md-12 mb-3">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control"
                       onchange="previewImage(this, 'productPreview')">
                <img id="productPreview" style="width:120px; display:none; margin-top:10px;">
            </div>
            <hr>

            <!-- SEO Meta -->
            <h4 class="mt-4">SEO Meta Information</h4>
            <div class="col-md-6 mb-3">
                <label>SEO Title</label>
                <input type="text" name="seo_meta_title" id="seo_meta_title" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>SEO Keywords</label>
                <input type="text" name="seo_meta_key" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>SEO Description</label>
                <textarea name="seo_meta_description" id="seo_meta_description" class="form-control"></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>SEO Image</label>
                <input type="file" name="seo_meta_image" class="form-control"
                       onchange="previewImage(this, 'seoPreview')">
                <img id="seoPreview" style="width:120px; display:none; margin-top:10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label>Canonical URL</label>
                <input type="text" name="seo_canonical" class="form-control">
            </div>
            <hr>

            <!-- OG Meta -->
            <h4 class="mt-4">Open Graph (OG) Meta</h4>
            <div class="col-md-6 mb-3">
                <label>OG Title</label>
                <input type="text" name="og_meta_title" id="og_meta_title" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>OG Keywords</label>
                <input type="text" name="og_meta_key" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>OG Description</label>
                <textarea name="og_meta_description" id="og_meta_description" class="form-control"></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>OG Image</label>
                <input type="file" name="og_meta_image" class="form-control"
                       onchange="ogPreview(this)">
                <img id="ogPreviewImg" style="width:120px; display:none; margin-top:10px;">
            </div>
            <!-- Status -->
            <div class="col-md-6 mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <button class="btn btn-success mt-3">Submit</button>
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

    // Live preview bindings
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
