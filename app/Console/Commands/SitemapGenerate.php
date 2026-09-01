<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;

class SitemapGenerate extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate a static public/sitemap.xml file';

    public function handle()
    {
        $controller = new SitemapController();
        $controller->writeFile();

        $this->info('sitemap.xml generated successfully at public/sitemap.xml');

        return self::SUCCESS;
    }
}
