<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $attrs = []): Product
    {
        $name = $attrs['product_name'] ?? 'Sample Product';

        return Product::create(array_merge([
            'product_name' => $name,
            'slug'         => ($attrs['slug'] ?? (\Illuminate\Support\Str::slug($name) . '-' . substr(md5(uniqid()), 0, 6))),
            'price'        => 100,
            'status'       => 1,
        ], $attrs));
    }

    public function test_products_index_loads_with_search_filter_sort()
    {
        $this->makeProduct(['product_name' => 'Alpha Shoe', 'status' => 1]);
        $this->makeProduct(['product_name' => 'Beta Shoe', 'status' => 0]);

        $this->get(route('products.index'))->assertStatus(200)->assertSee('Alpha Shoe')->assertSee('Beta Shoe');
        $this->get(route('products.index', ['q' => 'Alpha']))->assertSee('Alpha Shoe')->assertDontSee('Beta Shoe');
        $this->get(route('products.index', ['status' => '0']))->assertSee('Beta Shoe')->assertDontSee('Alpha Shoe');
        $this->get(route('products.index', ['sort' => 'name_asc']))->assertStatus(200);
    }

    public function test_store_creates_slug_and_tags_and_redirects()
    {
        $cat = Category::create(['name' => 'Footwear', 'slug' => 'footwear', 'status' => 1]);

        $response = $this->post(route('products.store'), [
            'product_name'    => 'Cool Sneaker',
            'price'           => 999.99,
            'category_id'     => $cat->id,
            'tags'            => 'red, summer, sale',
            'seo_meta_title'  => 'Cool Sneaker SEO',
            'status'          => 1,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['slug' => 'cool-sneaker']);

        $product = Product::where('slug', 'cool-sneaker')->first();
        $this->assertCount(3, $product->tags);
        $this->assertEquals('Footwear', $product->category->name);
    }

    public function test_slug_is_unique_when_name_repeats()
    {
        $this->post(route('products.store'), ['product_name' => 'Same Name', 'price' => 10]);
        $this->post(route('products.store'), ['product_name' => 'Same Name', 'price' => 10]);

        $this->assertDatabaseHas('products', ['slug' => 'same-name']);
        $this->assertDatabaseHas('products', ['slug' => 'same-name-1']);
    }

    public function test_show_page_injects_json_ld_twitter_and_hreflang()
    {
        $product = $this->makeProduct([
            'product_name'   => 'Meta Product',
            'slug'           => 'meta-product',
            'og_meta_title'  => 'OG Meta Product',
            'og_meta_image'  => 'og.jpg',
            'seo_meta_title' => 'SEO Meta Product',
        ]);

        $response = $this->get(route('products.show', $product));
        $response->assertStatus(200);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type": "Product"', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('twitter:card', false);
        $response->assertSee('summary_large_image', false);
        $response->assertSee('twitter:title', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="gu"', false);
        $response->assertSee('hreflang="hi"', false);
        $response->assertSee('hreflang="x-default"', false);
        $response->assertSee('SEO Audit', false);
    }

    public function test_soft_delete_restore_and_force_delete()
    {
        $product = $this->makeProduct(['product_name' => 'Trash Me']);

        $this->delete(route('products.destroy', $product))->assertRedirect();
        $this->get(route('products.index'))->assertDontSee('Trash Me');
        $this->get(route('products.index', ['trash' => 1]))->assertSee('Trash Me');

        $this->get(route('products.restore', $product->id))->assertRedirect();
        $this->get(route('products.index'))->assertSee('Trash Me');

        $this->delete(route('products.forceDelete', $product->id))->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_bulk_actions_change_status_and_delete()
    {
        $a = $this->makeProduct(['status' => 1]);
        $b = $this->makeProduct(['status' => 1]);

        $this->post(route('products.bulk'), ['ids' => [$a->id, $b->id], 'action' => 'inactive'])->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $a->id, 'status' => 0]);
        $this->assertDatabaseHas('products', ['id' => $b->id, 'status' => 0]);

        $this->post(route('products.bulk'), ['ids' => [$a->id, $b->id], 'action' => 'delete'])->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $a->id]);
    }

    public function test_categories_crud()
    {
        $this->get(route('categories.index'))->assertStatus(200);
        $this->post(route('categories.store'), ['name' => 'Books', 'slug' => 'books', 'status' => 1])->assertRedirect();
        $this->assertDatabaseHas('categories', ['slug' => 'books']);

        $cat = Category::where('slug', 'books')->first();
        $this->put(route('categories.update', $cat), ['name' => 'Ebooks', 'slug' => 'ebooks', 'status' => 1])->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Ebooks']);

        $this->delete(route('categories.destroy', $cat))->assertRedirect();
    }

    public function test_sitemap_and_robots_endpoints()
    {
        $this->makeProduct(['slug' => 'listed', 'status' => 1]);

        $this->get('/sitemap.xml')
             ->assertStatus(200)
             ->assertHeader('Content-Type', 'application/xml')
             ->assertSee('<urlset', false)
             ->assertSee('/products/show/listed', false);

        $this->get('/robots.txt')
             ->assertStatus(200)
             ->assertSee('User-agent', false);
    }

    public function test_export_and_import_csv()
    {
        $this->makeProduct(['product_name' => 'Exported Item']);

        $this->get(route('products.export'))
             ->assertStatus(200)
             ->assertSee('product_name')
             ->assertSee('Exported Item');

        $csv = "product_name,price,size,color,description,status,tags\nImported Item,150,,blue,desc,active,\"new,hot\"\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('products.csv', $csv, 'text/csv');

        $this->post(route('products.import.store'), ['file' => $file])->assertRedirect();

        $this->assertDatabaseHas('products', ['product_name' => 'Imported Item', 'slug' => 'imported-item']);
        $this->assertDatabaseHas('tags', ['name' => 'new']);
    }
}
