<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site defaults used for SEO meta, sitemap and hreflang generation
    |--------------------------------------------------------------------------
    */

    'site_name' => env('SEO_SITE_NAME', 'My Shop'),

    'default_locale' => env('SEO_DEFAULT_LOCALE', 'en'),

    /*
    | Supported languages for hreflang alternate links.
    | Each entry maps a hreflang code to its label.
    */
    'locales' => [
        'en' => 'English',
        'gu' => 'Gujarati',
        'hi' => 'Hindi',
    ],

    /*
    | Robots.txt default content used when no static file exists.
    */
    'robots_default' => "User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: " . (env('APP_URL', 'http://localhost') . '/sitemap.xml'),

];
