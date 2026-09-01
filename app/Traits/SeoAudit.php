<?php

namespace App\Traits;

use App\Models\Product;

trait SeoAudit
{
    /**
     * Analyse a product's SEO completeness and return a checklist.
     *
     * @return array<int, array{label: string, status: string, message: string}>
     */
    public static function audit(Product $product): array
    {
        $checks = [];

        // SEO Title
        $title = (string) $product->seo_meta_title ?: (string) $product->product_name;
        $titleLen = strlen($title);
        $checks[] = [
            'label'   => 'SEO Title',
            'status'  => $titleLen >= 30 && $titleLen <= 60 ? 'good' : ($titleLen > 0 ? 'warn' : 'bad'),
            'message' => $titleLen > 0
                ? "Length {$titleLen} chars (recommended 30-60)"
                : 'Missing',
        ];

        // SEO Description
        $desc = (string) $product->seo_meta_description;
        $descLen = strlen($desc);
        $checks[] = [
            'label'   => 'SEO Description',
            'status'  => $descLen >= 70 && $descLen <= 160 ? 'good' : ($descLen > 0 ? 'warn' : 'bad'),
            'message' => $descLen > 0
                ? "Length {$descLen} chars (recommended 70-160)"
                : 'Missing',
        ];

        // Keywords
        $checks[] = [
            'label'   => 'SEO Keywords',
            'status'  => ! empty($product->seo_meta_key) ? 'good' : 'bad',
            'message' => ! empty($product->seo_meta_key) ? 'Present' : 'Missing',
        ];

        // Canonical
        $checks[] = [
            'label'   => 'Canonical URL',
            'status'  => ! empty($product->seo_canonical) ? 'good' : 'warn',
            'message' => ! empty($product->seo_canonical) ? 'Set' : 'Not set (self-referencing recommended)',
        ];

        // Slug
        $checks[] = [
            'label'   => 'SEO Friendly URL (slug)',
            'status'  => ! empty($product->slug) ? 'good' : 'bad',
            'message' => ! empty($product->slug) ? $product->slug : 'Missing',
        ];

        // OG Title
        $checks[] = [
            'label'   => 'OG Title',
            'status'  => ! empty($product->og_meta_title) ? 'good' : 'warn',
            'message' => ! empty($product->og_meta_title) ? 'Present' : 'Missing (falls back to product name)',
        ];

        // OG Description
        $checks[] = [
            'label'   => 'OG Description',
            'status'  => ! empty($product->og_meta_description) ? 'good' : 'warn',
            'message' => ! empty($product->og_meta_description) ? 'Present' : 'Missing',
        ];

        // OG Image
        $checks[] = [
            'label'   => 'OG Image',
            'status'  => ! empty($product->og_meta_image) ? 'good' : 'bad',
            'message' => ! empty($product->og_meta_image) ? 'Present' : 'Missing',
        ];

        // SEO Image
        $checks[] = [
            'label'   => 'SEO Image',
            'status'  => ! empty($product->seo_meta_image) ? 'good' : 'warn',
            'message' => ! empty($product->seo_meta_image) ? 'Present' : 'Missing',
        ];

        return $checks;
    }
}
