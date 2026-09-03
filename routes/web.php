<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SeoDashboardController;


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| SEO Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/seo-dashboard',
    [SeoDashboardController::class, 'index']
)->name('seo.dashboard');

Route::get(
    '/admin/seo-dashboard/export',
    [SeoDashboardController::class, 'export']
)->name('seo.dashboard.export');


/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

Route::get(
    '/sitemap.xml',
    [SitemapController::class, 'index']
);

Route::get(
    '/robots.txt',
    [RobotsController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

Route::get(
    '/products',
    [ProductController::class, 'index']
)->name('products.index');

Route::get(
    '/products/create',
    [ProductController::class, 'create']
)->name('products.create');

Route::post(
    '/products/store',
    [ProductController::class, 'store']
)->name('products.store');

Route::get(
    '/products/import',
    [ProductController::class, 'importForm']
)->name('products.import');

Route::post(
    '/products/import',
    [ProductController::class, 'import']
)->name('products.import.store');

Route::get(
    '/products/export',
    [ProductController::class, 'export']
)->name('products.export');

Route::post(
    '/products/bulk',
    [ProductController::class, 'bulk']
)->name('products.bulk');


/*
|--------------------------------------------------------------------------
| NEW PRODUCT FEATURES
|--------------------------------------------------------------------------
*/

/* Quick Active / Inactive */
Route::patch(
    '/products/{product}/toggle-status',
    [ProductController::class, 'toggleStatus']
)->name('products.toggleStatus');


/* Duplicate */
Route::post(
    '/products/{product}/duplicate',
    [ProductController::class, 'duplicate']
)->name('products.duplicate');


/* Bulk Restore */
Route::post(
    '/products/bulk-restore',
    [ProductController::class, 'bulkRestore']
)->name('products.bulkRestore');


/* Empty Trash */
Route::delete(
    '/products/empty-trash',
    [ProductController::class, 'emptyTrash']
)->name('products.emptyTrash');


/*
|--------------------------------------------------------------------------
| Product CRUD
|--------------------------------------------------------------------------
*/

Route::get(
    '/products/edit/{product}',
    [ProductController::class, 'edit']
)->name('products.edit');

Route::put(
    '/products/update/{product}',
    [ProductController::class, 'update']
)->name('products.update');

Route::delete(
    '/products/delete/{product}',
    [ProductController::class, 'destroy']
)->name('products.destroy');

Route::get(
    '/products/show/{product}',
    [ProductController::class, 'show']
)->name('products.show');


/*
|--------------------------------------------------------------------------
| Trash
|--------------------------------------------------------------------------
*/

Route::get(
    '/products/restore/{id}',
    [ProductController::class, 'restore']
)->name('products.restore');

Route::delete(
    '/products/force-delete/{id}',
    [ProductController::class, 'forceDelete']
)->name('products.forceDelete');


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

Route::get(
    '/categories',
    [CategoryController::class, 'index']
)->name('categories.index');

Route::get(
    '/categories/create',
    [CategoryController::class, 'create']
)->name('categories.create');

Route::post(
    '/categories/store',
    [CategoryController::class, 'store']
)->name('categories.store');

Route::get(
    '/categories/edit/{category}',
    [CategoryController::class, 'edit']
)->name('categories.edit');

Route::put(
    '/categories/update/{category}',
    [CategoryController::class, 'update']
)->name('categories.update');

Route::delete(
    '/categories/delete/{category}',
    [CategoryController::class, 'destroy']
)->name('categories.destroy');


/*
|--------------------------------------------------------------------------
| Robots Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/robots',
    [RobotsController::class, 'edit']
)->name('robots.edit');

Route::post(
    '/admin/robots',
    [RobotsController::class, 'update']
)->name('robots.update');
