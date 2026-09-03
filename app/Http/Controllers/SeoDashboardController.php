<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\SeoAudit;
use Illuminate\Http\Request;

class SeoDashboardController extends Controller
{
    use SeoAudit;

    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->where('status', 1);

        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('focus_keyword', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->latest()
            ->get();

        $rows = $products->map(function (Product $product) {
            $score = self::seoScore($product);

            return [
                'product' => $product,
                'score' => $score,
                'status' => self::seoScoreStatus($score),
            ];
        });

        if ($request->filled('score')) {
            $scoreFilter = $request->score;

            $rows = $rows->filter(function ($row) use ($scoreFilter) {
                return match ($scoreFilter) {
                    'excellent' => $row['score'] >= 90,
                    'good' => $row['score'] >= 75 && $row['score'] < 90,
                    'warning' => $row['score'] >= 50 && $row['score'] < 75,
                    'critical' => $row['score'] < 50,
                    default => true,
                };
            })->values();
        }

        $totalProducts = $rows->count();

        $excellent = $rows->where('score', '>=', 90)->count();

        $good = $rows->filter(
            fn ($row) => $row['score'] >= 75 && $row['score'] < 90
        )->count();

        $needsImprovement = $rows->filter(
            fn ($row) => $row['score'] >= 50 && $row['score'] < 75
        )->count();

        $critical = $rows->where('score', '<', 50)->count();

        $averageScore = $totalProducts > 0
            ? (int) round($rows->avg('score'))
            : 0;

        return view('seo.dashboard', compact(
            'rows',
            'totalProducts',
            'excellent',
            'good',
            'needsImprovement',
            'critical',
            'averageScore'
        ));
    }
}