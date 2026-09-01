<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    // Dynamic sitemap (works when no static public/sitemap.xml exists)
    public function index()
    {
        $xml = $this->generate();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    // Artisan command: php artisan sitemap:generate
    public function generate(): string
    {
        $urls = [];
        $urls[] = [
            'loc'     => url('/'),
            'lastmod' => now()->toAtomString(),
        ];

        foreach (Product::where('status', 1)->get() as $product) {
            $urls[] = [
                'loc'     => url("/products/show/{$product->slug}"),
                'lastmod' => $product->updated_at->toAtomString(),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($urls as $u) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . e($u['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $u['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }
        $xml .= '</urlset>';

        return $xml;
    }

    // Write static file to public/sitemap.xml
    public function writeFile(): void
    {
        file_put_contents(public_path('sitemap.xml'), $this->generate());
    }
}
