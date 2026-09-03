<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Traits\SeoAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use SeoAudit;

    // -------------------------------
    // SHOW PRODUCT LIST (search / filter / sort / trash)
    // -------------------------------
    public function index(Request $request)
    {
        $query = Product::query()->with('category', 'tags');

        // Search by name / description
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($b) use ($q) {
                $b->where('product_name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Trash view
        if ($request->boolean('trash')) {
            $query->onlyTrashed();
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort === 'name_asc') {
            $query->orderBy('product_name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('product_name', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products   = $query->paginate(10)->withQueryString();
        $categories = Category::where('status', 1)->get();

        return view('products.index', compact('products', 'categories'));
    }

    // -------------------------------
    // SHOW CREATE FORM
    // -------------------------------
    public function create()
    {
        $categories = Category::where('status', 1)->get();

        return view('products.create', compact('categories'));
    }

    // -------------------------------
    // STORE NEW PRODUCT
    // -------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'product_name'    => 'required|regex:/^[A-Za-z\s]+$/',
            'color'           => 'nullable|regex:/^[A-Za-z\s]+$/',
            'price'           => 'required|numeric',
            'category_id'     => 'nullable|exists:categories,id',
            'focus_keyword'  => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'seo_meta_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'og_meta_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $mainImage = $this->uploadImage($request, 'image', 'product_');
        $seoImage  = $this->uploadImage($request, 'seo_meta_image', 'seo_');
        $ogImage   = $this->uploadImage($request, 'og_meta_image', 'og_');

        $product = Product::create([
            'product_name'         => $request->product_name,
            'slug'                 => $this->uniqueSlug($request->product_name),
            'image'                => $mainImage,
            'price'                => $request->price,
            'size'                 => $request->size,
            'color'                => $request->color,
            'description'          => $request->description,
            'category_id'          => $request->category_id,

            'seo_meta_title'       => $request->seo_meta_title,
            'seo_meta_description' => $request->seo_meta_description,
            'seo_meta_key'         => $request->seo_meta_key,
            'focus_keyword' => $request->focus_keyword,
            'seo_meta_image'       => $seoImage,
            'seo_canonical'        => $request->seo_canonical,

            'og_meta_title'        => $request->og_meta_title,
            'og_meta_description'  => $request->og_meta_description,
            'og_meta_key'          => $request->og_meta_key,
            'og_meta_image'        => $ogImage,

            'status'               => $request->status ?? 1,
        ]);

        $this->syncTags($product, $request->tags);

        return redirect()->route('products.index')->with('success', 'Product Added Successfully');
    }

    // -------------------------------
    // EDIT PRODUCT PAGE
    // -------------------------------
    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->get();
        $productTags = $product->tags->pluck('name')->implode(', ');

        return view('products.edit', compact('product', 'categories', 'productTags'));
    }

    // -------------------------------
    // SHOW PRODUCT DETAILS (+ SEO audit + JSON-LD)
    // -------------------------------
    public function show(Product $product)
    {
        $seoAudit = self::audit($product);
        $jsonLd   = $this->buildJsonLd($product);

        return view('products.show', compact('product', 'seoAudit', 'jsonLd'));
    }

    // -------------------------------
    // UPDATE EXISTING PRODUCT
    // -------------------------------
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name'    => 'required|regex:/^[A-Za-z\s]+$/',
            'color'           => 'nullable|regex:/^[A-Za-z\s]+$/',
            'price'           => 'required|numeric',
            'category_id'     => 'nullable|exists:categories,id',
            'focus_keyword'   => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'seo_meta_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'og_meta_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $mainImage = $this->updateImage($request, 'image', 'product_', $product->image);
        $seoImage  = $this->updateImage($request, 'seo_meta_image', 'seo_', $product->seo_meta_image);
        $ogImage   = $this->updateImage($request, 'og_meta_image', 'og_', $product->og_meta_image);

        $product->update([
            'product_name'         => $request->product_name,
            'slug'                 => $this->uniqueSlug($request->product_name, $product->id),
            'image'                => $mainImage,
            'price'                => $request->price,
            'size'                 => $request->size,
            'color'                => $request->color,
            'description'          => $request->description,
            'category_id'          => $request->category_id,

            'seo_meta_title'       => $request->seo_meta_title,
            'seo_meta_description' => $request->seo_meta_description,
            'seo_meta_key'         => $request->seo_meta_key,
            'focus_keyword'        => $request->focus_keyword,
            'seo_meta_image'       => $seoImage,
            'seo_canonical'        => $request->seo_canonical,

            'og_meta_title'        => $request->og_meta_title,
            'og_meta_description'  => $request->og_meta_description,
            'og_meta_key'          => $request->og_meta_key,
            'og_meta_image'        => $ogImage,

            'status'               => $request->status ?? 1,
        ]);

        $this->syncTags($product, $request->tags);

        return redirect()->route('products.index')->with('success', 'Product Updated Successfully');
    }

    // -------------------------------
    // SOFT DELETE PRODUCT + IMAGES
    // -------------------------------
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product Moved to Trash');
    }

    // -------------------------------
    // BULK ACTIONS (delete / status)
    // -------------------------------
    public function bulk(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'integer',
            'action' => 'required|in:delete,active,inactive',
        ]);

        $ids = $request->ids;

        if ($request->action === 'delete') {
            Product::whereIn('id', $ids)->get()->each->delete();
            $msg = 'Selected products moved to trash';
        } elseif ($request->action === 'active') {
            Product::whereIn('id', $ids)->update(['status' => 1]);
            $msg = 'Selected products activated';
        } else {
            Product::whereIn('id', $ids)->update(['status' => 0]);
            $msg = 'Selected products deactivated';
        }

        return redirect()->route('products.index')->with('success', $msg);
    }

    // -------------------------------
    // RESTORE FROM TRASH
    // -------------------------------
    public function restore($id)
    {
        Product::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('products.index', ['trash' => 1])->with('success', 'Product Restored');
    }

    // -------------------------------
    // FORCE DELETE (permanent)
    // -------------------------------
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        foreach ([$product->image, $product->seo_meta_image, $product->og_meta_image] as $file) {
            if ($file && file_exists(public_path('images/' . $file))) {
                unlink(public_path('images/' . $file));
            }
        }

        $product->forceDelete();

        return redirect()->route('products.index', ['trash' => 1])->with('success', 'Product Permanently Deleted');
    }

    // -------------------------------
    // EXPORT CSV
    // -------------------------------
    public function export()
    {
        $products = Product::withTrashed()->with('category', 'tags')->get();

        $columns = [
            'id',
            'product_name',
            'slug',
            'price',
            'size',
            'color',
            'description',
            'category',
            'tags',
            'status',
            'focus_keyword',
            'seo_meta_title',
            'seo_meta_description',
            'seo_meta_key',
            'seo_canonical',
            'og_meta_title',
            'og_meta_description',
            'og_meta_key',
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $columns);

        foreach ($products as $p) {
            fputcsv($output, [
                $p->id,
                $p->product_name,
                $p->slug,
                $p->price,
                $p->size,
                $p->color,
                $p->description,
                $p->category?->name,
                $p->tags->pluck('name')->implode(', '),
                $p->status ? 'active' : 'inactive',
                $p->focus_keyword,
                $p->seo_meta_title,
                $p->seo_meta_description,
                $p->seo_meta_key,
                $p->seo_canonical,
                $p->og_meta_title,
                $p->og_meta_description,
                $p->og_meta_key,
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_' . date('Y-m-d') . '.csv"',
        ]);
    }

    // -------------------------------
    // IMPORT FORM
    // -------------------------------
    public function importForm()
    {
        return view('products.import');
    }

    // -------------------------------
    // IMPORT CSV
    // -------------------------------
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_shift($rows));

        $count = 0;
        foreach ($rows as $row) {
            if (count($row) < count($header)) {
                continue;
            }
            $data = array_combine($header, $row);

            if (empty($data['product_name']) || ! isset($data['price'])) {
                continue;
            }

            $product = Product::create([
                'product_name' => $data['product_name'],
                'slug'         => $this->uniqueSlug($data['product_name']),
                'price'        => $data['price'],
                'size'         => $data['size'] ?? null,
                'color'        => $data['color'] ?? null,
                'description'  => $data['description'] ?? null,
                'focus_keyword' => $data['focus_keyword'] ?? null,
                'status'       => isset($data['status']) && $data['status'] === 'inactive' ? 0 : 1,
            ]);

            if (! empty($data['tags'])) {
                $this->syncTags($product, $data['tags']);
            }
            $count++;
        }

        return redirect()->route('products.index')->with('success', "{$count} products imported");
    }

    // ===============================
    // HELPERS
    // ===============================

    private function uploadImage(Request $request, string $field, string $prefix): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }
        $name = $prefix . time() . '_' . uniqid() . '.' . $request->file($field)->extension();
        $request->file($field)->move(public_path('images'), $name);

        return $name;
    }

    private function updateImage(Request $request, string $field, string $prefix, ?string $old): ?string
    {
        if (! $request->hasFile($field)) {
            return $old;
        }
        if ($old && file_exists(public_path('images/' . $old))) {
            unlink(public_path('images/' . $old));
        }
        $name = $prefix . time() . '_' . uniqid() . '.' . $request->file($field)->extension();
        $request->file($field)->move(public_path('images'), $name);

        return $name;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'product';
        $original = $slug;
        $i = 1;
        while (Product::withTrashed()->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    private function syncTags(Product $product, ?string $tags): void
    {
        if (empty($tags)) {
            $product->tags()->detach();

            return;
        }

        $ids = [];
        foreach (array_filter(array_map('trim', explode(',', $tags))) as $tag) {
            $ids[] = Tag::firstOrCreate(
                ['slug' => Str::slug($tag) ?: Str::random(6)],
                ['name' => $tag]
            )->id;
        }
        $product->tags()->sync($ids);
    }

    private function buildJsonLd(Product $product): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'         => 'Product',
                    'name'          => $product->product_name,
                    'description'   => $product->seo_meta_description ?: strip_tags((string) $product->description),
                    'url'           => url("/products/show/{$product->slug}"),
                    'sku'           => (string) $product->id,
                    'offers'        => [
                        '@type'         => 'Offer',
                        'price'         => $product->price,
                        'priceCurrency' => 'INR',
                        'availability'  => $product->status
                            ? 'https://schema.org/InStock'
                            : 'https://schema.org/OutOfStock',
                    ],
                ],
                [
                    '@type'    => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type'    => 'ListItem',
                            'position' => 1,
                            'name'     => config('seo.site_name'),
                            'item'     => url('/'),
                        ],
                        [
                            '@type'    => 'ListItem',
                            'position' => 2,
                            'name'     => $product->product_name,
                            'item'     => url("/products/show/{$product->slug}"),
                        ],
                    ],
                ],
            ],
        ];

        if ($product->image) {
            $schema['@graph'][0]['image'] = asset('images/' . $product->image);
        }

        return $schema;
    }
}
