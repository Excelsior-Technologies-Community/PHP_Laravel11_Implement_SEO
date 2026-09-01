<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RobotsController;

Route::get('/', function () {
    return view('welcome');
});

// SEO endpoints
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [RobotsController::class, 'index']);

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/import', [ProductController::class, 'importForm'])->name('products.import');
Route::post('/products/import', [ProductController::class, 'import'])->name('products.import.store');
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::post('/products/bulk', [ProductController::class, 'bulk'])->name('products.bulk');

Route::get('/products/edit/{product}', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/update/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/delete/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::get('/products/show/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/products/restore/{id}', [ProductController::class, 'restore'])->name('products.restore');
Route::delete('/products/force-delete/{id}', [ProductController::class, 'forceDelete'])->name('products.forceDelete');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/update/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/delete/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

// Robots management
Route::get('/admin/robots', [RobotsController::class, 'edit'])->name('robots.edit');
Route::post('/admin/robots', [RobotsController::class, 'update'])->name('robots.update');
