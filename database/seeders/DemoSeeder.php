<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        function fetchImage(string $url, string $path): ?string {
            $ctx = stream_context_create(['http' => ['timeout' => 20, 'user_agent' => 'Mozilla/5.0']]);
            $data = @file_get_contents($url, false, $ctx);
            if ($data !== false && file_put_contents($path, $data) !== false) {
                return basename($path);
            }
            return null;
        }

        $base = public_path('images');
        if (!is_dir($base)) {
            mkdir($base, 0777, true);
        }

        $cats = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'status' => 1],
            ['name' => 'Fashion', 'slug' => 'fashion', 'status' => 1],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'status' => 1],
            ['name' => 'Sports', 'slug' => 'sports', 'status' => 1],
        ];

        $catIds = [];
        foreach ($cats as $c) {
            $cat = Category::firstOrCreate(['slug' => $c['slug']], $c);
            $catIds[$c['slug']] = $cat->id;
        }

        $items = [
            ['name' => 'Wireless Headphones', 'cat' => 'electronics', 'price' => 1999, 'tags' => 'audio,wireless', 'ids' => [10, 11, 12], 'color' => 'Black'],
            ['name' => 'Running Shoes', 'cat' => 'fashion', 'price' => 2499, 'tags' => 'shoes,running', 'ids' => [20, 21, 22], 'color' => 'Red'],
            ['name' => 'Coffee Maker', 'cat' => 'home-kitchen', 'price' => 3499, 'tags' => 'kitchen,coffee', 'ids' => [30, 31, 32], 'color' => 'Silver'],
            ['name' => 'Yoga Mat', 'cat' => 'sports', 'price' => 999, 'tags' => 'yoga,fitness', 'ids' => [40, 41, 42], 'color' => 'Blue'],
            ['name' => 'Smart Watch', 'cat' => 'electronics', 'price' => 4999, 'tags' => 'wearable,smart', 'ids' => [50, 51, 52], 'color' => 'Black'],
            ['name' => 'Travel Backpack', 'cat' => 'fashion', 'price' => 1499, 'tags' => 'travel,bags', 'ids' => [60, 61, 62], 'color' => 'Green'],
            ['name' => 'Blender', 'cat' => 'home-kitchen', 'price' => 2199, 'tags' => 'kitchen,smoothie', 'ids' => [70, 71, 72], 'color' => 'White'],
            ['name' => 'Dumbbells Set', 'cat' => 'sports', 'price' => 2999, 'tags' => 'gym,strength', 'ids' => [80, 81, 82], 'color' => 'Orange'],
        ];

        foreach ($items as $it) {
            $products = Product::where('product_name', $it['name'])->withTrashed()->get();
            foreach ($products as $existing) {
                foreach (['image', 'seo_meta_image', 'og_meta_image'] as $field) {
                    $file = $existing->$field;
                    if ($file && file_exists($base . '/' . $file)) {
                        @unlink($base . '/' . $file);
                    }
                }
                $existing->tags()->detach();
                $existing->forceDelete();
            }
        }

        foreach ($items as $i => $it) {
            $slug = Str::slug($it['name']);

            $main = fetchImage("https://picsum.photos/id/{$it['ids'][0]}/800/800", $base . '/product_' . $slug . '.jpg');
            $seo  = fetchImage("https://picsum.photos/id/{$it['ids'][1]}/800/800", $base . '/seo_' . $slug . '.jpg');
            $og   = fetchImage("https://picsum.photos/id/{$it['ids'][2]}/800/800", $base . '/og_' . $slug . '.jpg');

            $product = Product::create([
                'product_name'         => $it['name'],
                'slug'                 => $slug,
                'price'                => $it['price'],
                'size'                 => 'Standard',
                'color'                => $it['color'],
                'description'          => "Premium quality {$it['name']} with excellent features. Perfect for everyday use.",
                'category_id'          => $catIds[$it['cat']],
                'seo_meta_title'       => "Buy {$it['name']} Online - Best Price",
                'seo_meta_description' => "Shop {$it['name']} at best price. Free shipping available. Check features, specs and reviews.",
                'seo_meta_key'         => $it['tags'],
                'seo_meta_image'       => $seo,
                'seo_canonical'        => url("/products/show/{$slug}"),
                'og_meta_title'        => "{$it['name']} - Great Deal",
                'og_meta_description'  => "Discover the best {$it['name']}. High quality, great price.",
                'og_meta_key'          => $it['tags'],
                'og_meta_image'        => $og,
                'status'               => 1,
            ]);

            $tagIds = [];
            foreach (explode(',', $it['tags']) as $t) {
                $tag = Tag::firstOrCreate(['slug' => Str::slug($t)], ['name' => trim($t)]);
                $tagIds[] = $tag->id;
            }
            $product->tags()->sync($tagIds);
        }
    }
}
