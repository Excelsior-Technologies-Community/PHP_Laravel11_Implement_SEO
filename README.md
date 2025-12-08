# 🚀 Laravel 11 Product CRUD With SEO + OG Meta  & Blade Layouts  
A modern, clean and production-ready CRUD system built using **Laravel 11**, **Blade Templates**, **Bootstrap 5**, and **SEO + OG Meta Features**.

This CRUD supports:

- Product creation  
- Image uploads (Main, SEO, OG)  
- SEO meta tags  
- Open Graph meta tags  
- Pagination  
- Dynamic layout system  
- Full create/read/update/delete workflow  

---

# 🎯 Features

- 📦 Full Product CRUD (Create, Read, Update, Delete)  
- 🖼 Image Upload (Main + SEO + OG Images)  
- 🔍 SEO Meta Support (Title, Desc, Keywords, Canonical)  
- 🌐 Open Graph Meta (Title, Desc, Image)  
- 🎨 Clean Blade Layout Architecture  
- 🧱 Bootstrap 5 UI  
- 🧩 Modular Views (index, create, edit, show)  
- ⏳ Pagination Enabled  
- 🛠 Fully Extensible Controller Logic  

---

# 📁 Project Folder Structure

```
app/
├── Http/Controllers/
│   ├── Controller.php
│   └── ProductController.php
├── Models/
│   └── Product.php
│
resources/
├── views/products/
│   ├── layout.blade.php
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
│
database/
├── migrations/
│   └── create_products_table.php
│
public/
└── images/   # Product, SEO & OG images stored here

routes/
└── web.php
```

---

# 📚 Table of Contents

- [Features](#-features)  
- [Project Folder Structure](#-project-folder-structure)  
- [Installation](#-installation)  
- [Environment Setup](#-environment-setup)  
- [Migration](#-migration)  
- [Routes](#-routes)  
- [Controller](#-controller)  
- [Model](#-model)  
- [Blade Views](#-blade-views)  
- [Run Application](#-run-application)

---

# ⚙️ Installation

Install Laravel 11:

```bash
composer create-project laravel/laravel blog "11.*"
```

---

# 🔧 Environment Setup

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

# 🗄️ Migration

Create migration:

```bash
php artisan make:migration create_products_table --create=products
```

Your migration includes:

- product_name  
- price, size, color  
- image, seo_meta_image, og_meta_image  
- SEO meta tags  
- OG meta tags  
- status  
- timestamps  

Run migration:

```bash
php artisan migrate
```

---

# 🛣️ Routes

```php
use App\Http\Controllers\ProductController;

Route::get('/', fn() => view('welcome'));

Route::get('/products', [ProductController::class,'index'])->name('products.index');
Route::get('/products/create', [ProductController::class,'create'])->name('products.create');
Route::post('/products/store', [ProductController::class,'store'])->name('products.store');
Route::get('/products/edit/{id}', [ProductController::class,'edit'])->name('products.edit');
Route::put('/products/update/{id}', [ProductController::class,'update'])->name('products.update');
Route::delete('/products/delete/{id}', [ProductController::class,'destroy'])->name('products.destroy');
Route::get('/products/show/{id}', [ProductController::class,'show'])->name('products.show');
```

---

# 🎮 Controller

A complete Product CRUD controller including:

- Validation  
- Image uploads  
- SEO + OG image handling  
- File replacement on update  
- File deletion on destroy  
- Pagination  
- Show, Edit, Delete logic  

**Example method (index):**

```php
public function index()
{
    $products = Product::latest()->paginate(10);
    return view('products.index', compact('products'));
}
```

(Full controller already exists inside your project.)

---

# 🧬 Model

```php
class Product extends Model
{
    protected $fillable = [
        'product_name','image','price','size','color','description',
        'seo_meta_title','seo_meta_description','seo_meta_key',
        'seo_meta_image','seo_canonical',
        'og_meta_title','og_meta_description','og_meta_key','og_meta_image',
        'status','created_by','updated_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];
}
```

---

# 🖼️ Blade Views

Your CRUD uses a **shared master layout**:

```
products/layout.blade.php
```

Child views:

- `index.blade.php` → Product table + pagination  
- `create.blade.php` → Create form + image previews  
- `edit.blade.php` → Update form + old images + previews  
- `show.blade.php` → SEO + OG meta injection + product details  

Each view uses:

✔ Bootstrap UI  
✔ Clean form groups  
✔ Error handling  
✔ Image preview scripts  

---

# ▶️ Run Application

```bash
php artisan serve
```

Visit:

```
http://localhost:8000/products
```

---

# 🎉 DONE!

Your **Laravel 11 Product CRUD System** is now fully ready with:

- SEO Meta  
- OG Meta  
- Multiple Image Upload  
- Laravel Validation  
- Blade Layout Architecture  
- Full CRUD Functionality
- 


INDEX PAGE:-

<img width="975" height="232" alt="image" src="https://github.com/user-attachments/assets/462ff460-4ea1-4b1e-9b39-50ab578d429f" />

CREATE PAGE:-

<img width="975" height="805" alt="image" src="https://github.com/user-attachments/assets/703aac53-932e-4c39-a2dc-0f2c9796dc93" />

EDIT PAGE:-

<img width="938" height="975" alt="image" src="https://github.com/user-attachments/assets/57a6406e-678a-4882-9216-8f7b4c345cf9" />

SEO & OG DATA SHOW PAGE:-

<img width="975" height="800" alt="image" src="https://github.com/user-attachments/assets/6efdc7a3-e2e4-491b-8226-4af720441f81" />



