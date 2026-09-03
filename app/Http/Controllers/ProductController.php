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

    /*
    |--------------------------------------------------------------------------
    | PRODUCT LIST
    |--------------------------------------------------------------------------
    | Search, category filter, status filter, price filter, tag filter,
    | sorting and trash.
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'tags']);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('focus_keyword', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | PRICE RANGE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_price')) {
            $query->where(
                'price',
                '>=',
                (float) $request->min_price
            );
        }

        if ($request->filled('max_price')) {
            $query->where(
                'price',
                '<=',
                (float) $request->max_price
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TAG FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('tag_id')) {
            $tagId = $request->tag_id;

            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | TRASH
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('trash')) {
            $query->onlyTrashed();
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'latest');

        switch ($sort) {

            case 'oldest':
                $query->oldest();
                break;

            case 'name_asc':
                $query->orderBy('product_name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('product_name', 'desc');
                break;

            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            default:
                $query->latest();
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | DROPDOWN DATA
        |--------------------------------------------------------------------------
        */

        $categories = Category::where('status', 1)
            ->orderBy('name')
            ->get();

        $tags = Tag::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | PRODUCT STATISTICS
        |--------------------------------------------------------------------------
        */

        $statisticsQuery = Product::query();

        if ($request->boolean('trash')) {
            $statisticsQuery->onlyTrashed();
        }

        $totalProducts = (clone $statisticsQuery)->count();

        $activeProducts = (clone $statisticsQuery)
            ->where('status', 1)
            ->count();

        $inactiveProducts = (clone $statisticsQuery)
            ->where('status', 0)
            ->count();

        $averagePrice = (clone $statisticsQuery)->avg('price') ?? 0;

        $highestPrice = (clone $statisticsQuery)->max('price') ?? 0;

        $lowestPrice = (clone $statisticsQuery)->min('price') ?? 0;

        return view('products.index', compact(
            'products',
            'categories',
            'tags',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'averagePrice',
            'highestPrice',
            'lowestPrice'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::where('status', 1)
            ->orderBy('name')
            ->get();

        $tags = Tag::orderBy('name')->get();

        return view(
            'products.create',
            compact('categories', 'tags')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'color' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'focus_keyword' => [
                'nullable',
                'string',
                'max:255',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'seo_meta_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'og_meta_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD IMAGES
        |--------------------------------------------------------------------------
        */

        $mainImage = $this->uploadImage(
            $request,
            'image',
            'product_'
        );

        $seoImage = $this->uploadImage(
            $request,
            'seo_meta_image',
            'seo_'
        );

        $ogImage = $this->uploadImage(
            $request,
            'og_meta_image',
            'og_'
        );

        /*
        |--------------------------------------------------------------------------
        | CREATE PRODUCT
        |--------------------------------------------------------------------------
        */

        $product = Product::create([
            'product_name' => $request->product_name,

            'slug' => $this->uniqueSlug(
                $request->product_name
            ),

            'image' => $mainImage,

            'price' => $request->price,

            'size' => $request->size,

            'color' => $request->color,

            'description' => $request->description,

            'category_id' => $request->category_id,

            /*
            | SEO
            */

            'seo_meta_title' => $request->seo_meta_title,

            'seo_meta_description' =>
                $request->seo_meta_description,

            'seo_meta_key' => $request->seo_meta_key,

            'focus_keyword' => $request->focus_keyword,

            'seo_meta_image' => $seoImage,

            'seo_canonical' => $request->seo_canonical,

            /*
            | Open Graph
            */

            'og_meta_title' => $request->og_meta_title,

            'og_meta_description' =>
                $request->og_meta_description,

            'og_meta_key' => $request->og_meta_key,

            'og_meta_image' => $ogImage,

            /*
            | Status
            */

            'status' => $request->has('status')
                ? (int) $request->status
                : 1,

            'created_by' => auth()->id(),

            'updated_by' => auth()->id(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | TAGS
        |--------------------------------------------------------------------------
        */

        $this->syncTags(
            $product,
            $request->tags
        );

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product Added Successfully'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)
            ->orderBy('name')
            ->get();

        $tags = Tag::orderBy('name')->get();

        $productTags = $product
            ->tags
            ->pluck('name')
            ->implode(', ');

        return view(
            'products.edit',
            compact(
                'product',
                'categories',
                'tags',
                'productTags'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Product $product)
    {
        $seoAudit = self::audit($product);

        $jsonLd = $this->buildJsonLd($product);

        return view(
            'products.show',
            compact(
                'product',
                'seoAudit',
                'jsonLd'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Product $product
    ) {
        $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'color' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'focus_keyword' => [
                'nullable',
                'string',
                'max:255',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'seo_meta_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'og_meta_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE IMAGES
        |--------------------------------------------------------------------------
        */

        $mainImage = $this->updateImage(
            $request,
            'image',
            'product_',
            $product->image
        );

        $seoImage = $this->updateImage(
            $request,
            'seo_meta_image',
            'seo_',
            $product->seo_meta_image
        );

        $ogImage = $this->updateImage(
            $request,
            'og_meta_image',
            'og_',
            $product->og_meta_image
        );

        /*
        |--------------------------------------------------------------------------
        | UPDATE PRODUCT
        |--------------------------------------------------------------------------
        */

        $product->update([
            'product_name' => $request->product_name,

            'slug' => $this->uniqueSlug(
                $request->product_name,
                $product->id
            ),

            'image' => $mainImage,

            'price' => $request->price,

            'size' => $request->size,

            'color' => $request->color,

            'description' => $request->description,

            'category_id' => $request->category_id,

            /*
            | SEO
            */

            'seo_meta_title' =>
                $request->seo_meta_title,

            'seo_meta_description' =>
                $request->seo_meta_description,

            'seo_meta_key' =>
                $request->seo_meta_key,

            'focus_keyword' =>
                $request->focus_keyword,

            'seo_meta_image' =>
                $seoImage,

            'seo_canonical' =>
                $request->seo_canonical,

            /*
            | Open Graph
            */

            'og_meta_title' =>
                $request->og_meta_title,

            'og_meta_description' =>
                $request->og_meta_description,

            'og_meta_key' =>
                $request->og_meta_key,

            'og_meta_image' =>
                $ogImage,

            /*
            | Status
            */

            'status' => $request->has('status')
                ? (int) $request->status
                : 1,

            'updated_by' => auth()->id(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE TAGS
        |--------------------------------------------------------------------------
        */

        $this->syncTags(
            $product,
            $request->tags
        );

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product Updated Successfully'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE / MOVE TO TRASH
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product Moved to Trash'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | BULK ACTION
    |--------------------------------------------------------------------------
    */

    public function bulk(Request $request)
    {
        $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'integer',
                'exists:products,id',
            ],

            'action' => [
                'required',
                'in:delete,active,inactive',
            ],
        ]);

        $ids = $request->ids;

        switch ($request->action) {

            case 'delete':

                Product::whereIn('id', $ids)
                    ->get()
                    ->each(function ($product) {
                        $product->delete();
                    });

                $message =
                    'Selected products moved to trash';

                break;

            case 'active':

                Product::whereIn('id', $ids)
                    ->update([
                        'status' => 1,
                        'updated_by' => auth()->id(),
                    ]);

                $message =
                    'Selected products activated';

                break;

            case 'inactive':

                Product::whereIn('id', $ids)
                    ->update([
                        'status' => 0,
                        'updated_by' => auth()->id(),
                    ]);

                $message =
                    'Selected products deactivated';

                break;

            default:

                $message =
                    'No action performed';

                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE STATUS
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(Product $product)
    {
        $product->update([
            'status' => $product->status ? 0 : 1,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Product status updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DUPLICATE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();

        /*
        | New product name
        */

        $newProduct->product_name =
            $product->product_name . ' Copy';

        /*
        | New unique slug
        */

        $newProduct->slug =
            $this->uniqueSlug(
                $newProduct->product_name
            );

        /*
        | User information
        */

        $newProduct->created_by =
            auth()->id();

        $newProduct->updated_by =
            auth()->id();

        /*
        | Save duplicate
        */

        $newProduct->save();

        /*
        | Copy tags
        */

        $newProduct->tags()->sync(
            $product->tags
                ->pluck('id')
                ->toArray()
        );

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product duplicated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function restore($id)
    {
        $product = Product::withTrashed()
            ->findOrFail($id);

        $product->restore();

        return redirect()
            ->route(
                'products.index',
                ['trash' => 1]
            )
            ->with(
                'success',
                'Product Restored Successfully'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | FORCE DELETE
    |--------------------------------------------------------------------------
    */

    public function forceDelete($id)
    {
        $product = Product::withTrashed()
            ->findOrFail($id);

        /*
        | Delete product images
        */

        $images = [
            $product->image,
            $product->seo_meta_image,
            $product->og_meta_image,
        ];

        foreach ($images as $file) {

            if (
                $file &&
                file_exists(
                    public_path(
                        'images/' . $file
                    )
                )
            ) {
                unlink(
                    public_path(
                        'images/' . $file
                    )
                );
            }
        }

        /*
        | Detach tags
        */

        $product->tags()->detach();

        /*
        | Permanently delete
        */

        $product->forceDelete();

        return redirect()
            ->route(
                'products.index',
                ['trash' => 1]
            )
            ->with(
                'success',
                'Product Permanently Deleted'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT CSV
    |--------------------------------------------------------------------------
    */

    public function export()
    {
        $products = Product::withTrashed()
            ->with(['category', 'tags'])
            ->latest()
            ->get();

        $filename =
            'products_' .
            now()->format('Y-m-d_H-i-s') .
            '.csv';

        $output = fopen(
            'php://temp',
            'r+'
        );

        /*
        | CSV HEADER
        */

        fputcsv($output, [
            'ID',
            'Product Name',
            'Slug',
            'Price',
            'Size',
            'Color',
            'Description',
            'Category',
            'Tags',
            'Status',
            'Focus Keyword',
            'SEO Meta Title',
            'SEO Meta Description',
            'SEO Meta Key',
            'SEO Canonical',
            'OG Meta Title',
            'OG Meta Description',
            'OG Meta Key',
            'Deleted At',
        ]);

        /*
        | CSV DATA
        */

        foreach ($products as $product) {

            fputcsv($output, [
                $product->id,

                $product->product_name,

                $product->slug,

                $product->price,

                $product->size,

                $product->color,

                $product->description,

                $product->category?->name,

                $product->tags
                    ->pluck('name')
                    ->implode(', '),

                $product->status
                    ? 'active'
                    : 'inactive',

                $product->focus_keyword,

                $product->seo_meta_title,

                $product->seo_meta_description,

                $product->seo_meta_key,

                $product->seo_canonical,

                $product->og_meta_title,

                $product->og_meta_description,

                $product->og_meta_key,

                $product->deleted_at,
            ]);
        }

        rewind($output);

        $csv = stream_get_contents($output);

        fclose($output);

        return Response::make(
            $csv,
            200,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    $filename .
                    '"',

                'Pragma' => 'no-cache',

                'Cache-Control' =>
                    'must-revalidate, post-check=0, pre-check=0',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT FORM
    |--------------------------------------------------------------------------
    */

    public function importForm()
    {
        return view('products.import');
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT CSV
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
        ]);

        $path =
            $request
                ->file('file')
                ->getRealPath();

        $file = fopen($path, 'r');

        if (! $file) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to read CSV file.'
                );
        }

        $header = fgetcsv($file);

        if (! $header) {
            fclose($file);

            return redirect()
                ->back()
                ->with(
                    'error',
                    'CSV file is empty.'
                );
        }

        $header = array_map(
            fn ($value) =>
                strtolower(trim($value)),
            $header
        );

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {

            if (
                count($row) !== count($header)
            ) {
                continue;
            }

            $data = array_combine(
                $header,
                $row
            );

            if (
                empty($data['product_name']) ||
                ! isset($data['price'])
            ) {
                continue;
            }

            /*
            | Category
            */

            $categoryId = null;

            if (! empty($data['category'])) {

                $category = Category::firstOrCreate(
                    [
                        'name' =>
                            trim($data['category']),
                    ],
                    [
                        'slug' =>
                            Str::slug(
                                $data['category']
                            ),
                        'status' => 1,
                    ]
                );

                $categoryId = $category->id;
            }

            /*
            | Product
            */

            $product = Product::create([
                'product_name' =>
                    trim($data['product_name']),

                'slug' =>
                    $this->uniqueSlug(
                        $data['product_name']
                    ),

                'price' =>
                    (float) $data['price'],

                'size' =>
                    $data['size'] ?? null,

                'color' =>
                    $data['color'] ?? null,

                'description' =>
                    $data['description'] ?? null,

                'category_id' =>
                    $categoryId,

                'focus_keyword' =>
                    $data['focus_keyword'] ?? null,

                'seo_meta_title' =>
                    $data['seo_meta_title'] ?? null,

                'seo_meta_description' =>
                    $data['seo_meta_description'] ?? null,

                'seo_meta_key' =>
                    $data['seo_meta_key'] ?? null,

                'seo_canonical' =>
                    $data['seo_canonical'] ?? null,

                'og_meta_title' =>
                    $data['og_meta_title'] ?? null,

                'og_meta_description' =>
                    $data['og_meta_description'] ?? null,

                'og_meta_key' =>
                    $data['og_meta_key'] ?? null,

                'status' =>
                    isset($data['status']) &&
                    strtolower(
                        trim($data['status'])
                    ) === 'inactive'
                        ? 0
                        : 1,

                'created_by' =>
                    auth()->id(),

                'updated_by' =>
                    auth()->id(),
            ]);

            /*
            | Tags
            */

            if (! empty($data['tags'])) {

                $this->syncTags(
                    $product,
                    $data['tags']
                );
            }

            $count++;
        }

        fclose($file);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                "{$count} products imported successfully."
            );
    }

    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    private function uploadImage(
        Request $request,
        string $field,
        string $prefix
    ): ?string {

        if (! $request->hasFile($field)) {
            return null;
        }

        $directory =
            public_path('images');

        if (! is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }

        $file = $request->file($field);

        $name =
            $prefix .
            time() .
            '_' .
            uniqid() .
            '.' .
            $file->extension();

        $file->move(
            $directory,
            $name
        );

        return $name;
    }

    /*
    |--------------------------------------------------------------------------
    | IMAGE UPDATE
    |--------------------------------------------------------------------------
    */

    private function updateImage(
        Request $request,
        string $field,
        string $prefix,
        ?string $old
    ): ?string {

        if (! $request->hasFile($field)) {
            return $old;
        }

        /*
        | Delete old image
        */

        if (
            $old &&
            file_exists(
                public_path(
                    'images/' . $old
                )
            )
        ) {
            unlink(
                public_path(
                    'images/' . $old
                )
            );
        }

        return $this->uploadImage(
            $request,
            $field,
            $prefix
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE SLUG
    |--------------------------------------------------------------------------
    */

    private function uniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {

        $slug =
            Str::slug($name) ?: 'product';

        $original = $slug;

        $counter = 1;

        while (
            Product::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    function ($query) use ($ignoreId) {
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        );
                    }
                )
                ->exists()
        ) {

            $slug =
                $original .
                '-' .
                $counter++;

        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC TAGS
    |--------------------------------------------------------------------------
    */

    private function syncTags(
        Product $product,
        ?string $tags
    ): void {

        if (
            empty(
                trim((string) $tags)
            )
        ) {
            $product
                ->tags()
                ->detach();

            return;
        }

        $tagIds = [];

        $tagNames = array_filter(
            array_map(
                'trim',
                explode(',', $tags)
            )
        );

        foreach ($tagNames as $tagName) {

            $slug =
                Str::slug($tagName);

            if (! $slug) {
                $slug =
                    Str::random(6);
            }

            $tag = Tag::firstOrCreate(
                [
                    'slug' => $slug,
                ],
                [
                    'name' => $tagName,
                ]
            );

            $tagIds[] = $tag->id;
        }

        $product
            ->tags()
            ->sync($tagIds);
    }

    /*
    |--------------------------------------------------------------------------
    | JSON-LD
    |--------------------------------------------------------------------------
    */

    private function buildJsonLd(
        Product $product
    ): array {

        $productUrl =
            route(
                'products.show',
                $product
            );

        $schema = [

            '@context' =>
                'https://schema.org',

            '@graph' => [

                [

                    '@type' =>
                        'Product',

                    'name' =>
                        $product->product_name,

                    'description' =>
                        $product->seo_meta_description
                        ?: strip_tags(
                            (string)
                            $product->description
                        ),

                    'url' =>
                        $productUrl,

                    'sku' =>
                        (string) $product->id,

                    'offers' => [

                        '@type' =>
                            'Offer',

                        'price' =>
                            $product->price,

                        'priceCurrency' =>
                            'INR',

                        'availability' =>
                            $product->status
                                ? 'https://schema.org/InStock'
                                : 'https://schema.org/OutOfStock',
                    ],
                ],

                [

                    '@type' =>
                        'BreadcrumbList',

                    'itemListElement' => [

                        [

                            '@type' =>
                                'ListItem',

                            'position' =>
                                1,

                            'name' =>
                                config(
                                    'seo.site_name'
                                ),

                            'item' =>
                                url('/'),
                        ],

                        [

                            '@type' =>
                                'ListItem',

                            'position' =>
                                2,

                            'name' =>
                                $product->product_name,

                            'item' =>
                                $productUrl,
                        ],
                    ],
                ],
            ],
        ];

        /*
        | Product image
        */

        if ($product->image) {

            $schema['@graph'][0]['image'] =
                asset(
                    'images/' .
                    $product->image
                );
        }

        return $schema;
    }
}