<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Str;

trait SeoAudit
{
    /**
     * Analyse a product's SEO completeness and focus keyword optimization.
     *
     * @return array<int, array{
     *     label: string,
     *     status: string,
     *     message: string
     * }>
     */
    public static function audit(Product $product): array
    {
        $checks = [];

        /*
        |--------------------------------------------------------------------------
        | Basic SEO Checks
        |--------------------------------------------------------------------------
        */

        // SEO Title
        $title = trim((string) $product->seo_meta_title);
        $titleLen = mb_strlen($title);

        $checks[] = [
            'label' => 'SEO Title',
            'status' => $titleLen >= 30 && $titleLen <= 60
                ? 'good'
                : ($titleLen > 0 ? 'warn' : 'bad'),
            'message' => $titleLen > 0
                ? "Length {$titleLen} chars (recommended 30-60)"
                : 'Missing',
        ];

        // SEO Description
        $desc = trim((string) $product->seo_meta_description);
        $descLen = mb_strlen($desc);

        $checks[] = [
            'label' => 'SEO Description',
            'status' => $descLen >= 70 && $descLen <= 160
                ? 'good'
                : ($descLen > 0 ? 'warn' : 'bad'),
            'message' => $descLen > 0
                ? "Length {$descLen} chars (recommended 70-160)"
                : 'Missing',
        ];

        // SEO Keywords
        $checks[] = [
            'label' => 'SEO Keywords',
            'status' => !empty($product->seo_meta_key)
                ? 'good'
                : 'bad',
            'message' => !empty($product->seo_meta_key)
                ? 'Present'
                : 'Missing',
        ];

        // Canonical
        $checks[] = [
            'label' => 'Canonical URL',
            'status' => !empty($product->seo_canonical)
                ? 'good'
                : 'warn',
            'message' => !empty($product->seo_canonical)
                ? 'Set'
                : 'Not set (self-referencing recommended)',
        ];

        // Slug
        $checks[] = [
            'label' => 'SEO Friendly URL (slug)',
            'status' => !empty($product->slug)
                ? 'good'
                : 'bad',
            'message' => !empty($product->slug)
                ? $product->slug
                : 'Missing',
        ];

        // OG Title
        $checks[] = [
            'label' => 'OG Title',
            'status' => !empty($product->og_meta_title)
                ? 'good'
                : 'warn',
            'message' => !empty($product->og_meta_title)
                ? 'Present'
                : 'Missing (falls back to product name)',
        ];

        // OG Description
        $checks[] = [
            'label' => 'OG Description',
            'status' => !empty($product->og_meta_description)
                ? 'good'
                : 'warn',
            'message' => !empty($product->og_meta_description)
                ? 'Present'
                : 'Missing',
        ];

        // OG Image
        $checks[] = [
            'label' => 'OG Image',
            'status' => !empty($product->og_meta_image)
                ? 'good'
                : 'bad',
            'message' => !empty($product->og_meta_image)
                ? 'Present'
                : 'Missing',
        ];

        // SEO Image
        $checks[] = [
            'label' => 'SEO Image',
            'status' => !empty($product->seo_meta_image)
                ? 'good'
                : 'warn',
            'message' => !empty($product->seo_meta_image)
                ? 'Present'
                : 'Missing',
        ];

        /*
        |--------------------------------------------------------------------------
        | Focus Keyword Analysis
        |--------------------------------------------------------------------------
        */

        $keyword = trim((string) $product->focus_keyword);

        if (empty($keyword)) {
            $checks[] = [
                'label' => 'Focus Keyword',
                'status' => 'bad',
                'message' => 'Missing. Add a primary keyword for SEO analysis.',
            ];

            return $checks;
        }

        $keywordLower = mb_strtolower($keyword);

        $productName = mb_strtolower((string) $product->product_name);
        $seoTitle = mb_strtolower((string) $product->seo_meta_title);
        $seoDescription = mb_strtolower((string) $product->seo_meta_description);
        $slug = mb_strtolower((string) $product->slug);
        $description = mb_strtolower(strip_tags((string) $product->description));
        $seoKeywords = mb_strtolower((string) $product->seo_meta_key);
        $ogTitle = mb_strtolower((string) $product->og_meta_title);
        $ogDescription = mb_strtolower((string) $product->og_meta_description);

        // Keyword in Product Name
        $checks[] = [
            'label' => 'Focus Keyword in Product Name',
            'status' => Str::contains($productName, $keywordLower)
                ? 'good'
                : 'bad',
            'message' => Str::contains($productName, $keywordLower)
                ? 'Focus keyword found in product name.'
                : 'Focus keyword is not present in product name.',
        ];

        // Keyword in SEO Title
        $checks[] = [
            'label' => 'Focus Keyword in SEO Title',
            'status' => Str::contains($seoTitle, $keywordLower)
                ? 'good'
                : 'bad',
            'message' => Str::contains($seoTitle, $keywordLower)
                ? 'Focus keyword is present in SEO title.'
                : 'Focus keyword is not present in SEO title.',
        ];

        // Keyword in SEO Description
        $checks[] = [
            'label' => 'Focus Keyword in SEO Description',
            'status' => Str::contains($seoDescription, $keywordLower)
                ? 'good'
                : 'bad',
            'message' => Str::contains($seoDescription, $keywordLower)
                ? 'Focus keyword is present in SEO description.'
                : 'Focus keyword is not present in SEO description.',
        ];

        // Keyword in Slug
        $keywordSlug = Str::slug($keyword);

        $checks[] = [
            'label' => 'Focus Keyword in URL Slug',
            'status' => $keywordSlug !== ''
                && Str::contains($slug, $keywordSlug)
                ? 'good'
                : 'warn',
            'message' => $keywordSlug !== ''
                && Str::contains($slug, $keywordSlug)
                ? 'Focus keyword is present in URL slug.'
                : 'Consider including the focus keyword in the URL slug.',
        ];

        // Keyword in Content
        $checks[] = [
            'label' => 'Focus Keyword in Content',
            'status' => Str::contains($description, $keywordLower)
                ? 'good'
                : 'warn',
            'message' => Str::contains($description, $keywordLower)
                ? 'Focus keyword is present in product content.'
                : 'Focus keyword is not present in product content.',
        ];

        // Keyword in SEO Keywords
        $checks[] = [
            'label' => 'Focus Keyword in SEO Keywords',
            'status' => Str::contains($seoKeywords, $keywordLower)
                ? 'good'
                : 'warn',
            'message' => Str::contains($seoKeywords, $keywordLower)
                ? 'Focus keyword is listed in SEO keywords.'
                : 'Focus keyword is not listed in SEO keywords.',
        ];

        // Keyword in OG Title
        $checks[] = [
            'label' => 'Focus Keyword in OG Title',
            'status' => Str::contains($ogTitle, $keywordLower)
                ? 'good'
                : 'warn',
            'message' => Str::contains($ogTitle, $keywordLower)
                ? 'Focus keyword is present in OG title.'
                : 'Focus keyword is not present in OG title.',
        ];

        // Keyword in OG Description
        $checks[] = [
            'label' => 'Focus Keyword in OG Description',
            'status' => Str::contains($ogDescription, $keywordLower)
                ? 'good'
                : 'warn',
            'message' => Str::contains($ogDescription, $keywordLower)
                ? 'Focus keyword is present in OG description.'
                : 'Focus keyword is not present in OG description.',
        ];

        /*
        |--------------------------------------------------------------------------
        | Keyword Density
        |--------------------------------------------------------------------------
        */

        $plainContent = trim(
            $product->product_name . ' ' .
            strip_tags((string) $product->description)
        );

        $wordCount = self::wordCount($plainContent);

        $keywordCount = self::keywordOccurrences(
            mb_strtolower($plainContent),
            $keywordLower
        );

        $density = $wordCount > 0
            ? round(($keywordCount * str_word_count($keyword)) / $wordCount * 100, 2)
            : 0;

        $densityStatus = 'warn';

        if ($density >= 0.5 && $density <= 3) {
            $densityStatus = 'good';
        } elseif ($density > 3) {
            $densityStatus = 'bad';
        }

        $checks[] = [
            'label' => 'Focus Keyword Density',
            'status' => $densityStatus,
            'message' => "{$density}% keyword density ({$keywordCount} occurrence(s), {$wordCount} words). Recommended approximately 0.5%-3%.",
        ];

        return $checks;
    }

    /**
     * Calculate overall SEO score.
     */
    public static function seoScore(Product $product): int
    {
        $checks = self::audit($product);

        if (empty($checks)) {
            return 0;
        }

        $score = 0;

        foreach ($checks as $check) {
            if ($check['status'] === 'good') {
                $score += 10;
            } elseif ($check['status'] === 'warn') {
                $score += 5;
            }
        }

        $maxScore = count($checks) * 10;

        return $maxScore > 0
            ? (int) round(($score / $maxScore) * 100)
            : 0;
    }

    /**
     * Return human-readable SEO score status.
     */
    public static function seoScoreStatus(int $score): string
    {
        if ($score >= 90) {
            return 'Excellent';
        }

        if ($score >= 75) {
            return 'Good';
        }

        if ($score >= 50) {
            return 'Needs Improvement';
        }

        return 'Critical';
    }

    /**
     * Count words including multilingual text reasonably.
     */
    private static function wordCount(string $text): int
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text));
    }

    /**
     * Count occurrences of a phrase.
     */
    private static function keywordOccurrences(
        string $content,
        string $keyword
    ): int {
        if ($keyword === '') {
            return 0;
        }

        $count = 0;
        $offset = 0;

        while (($position = mb_strpos($content, $keyword, $offset)) !== false) {
            $count++;
            $offset = $position + mb_strlen($keyword);
        }

        return $count;
    }
}