

✨//Laravel Installation Code

⭐ composer create-project laravel/laravel blog "11.*"



✨//Migration Code

⭐php artisan make:migration create_products_table --create=products

  ⭐ php artisan migrate



✨//Controller & Model Creating Code

⭐php artisan make:controller ProductController --resource --model=Product



✨//This Code Is Show The Seo & Og meta data in Your Selected Show Page

⭐



@extends('products.layout') 

<!-- Set dynamic page title --> 

@section('title', $product->seo_meta_title ?? $product->product_name) 

<!-- SEO & OG META TAGS --> 

@section('meta') 

    <!-- SEO META TAGS --> 

    <meta name="title" content="{{ $product->seo_meta_title }}"> 

    <meta name="description" content="{{ $product->seo_meta_description }}"> 

    <meta name="keywords" content="{{ $product->seo_meta_key }}"> 

    @if($product->seo_canonical) 

        <link rel="canonical" href="{{ $product->seo_canonical }}"> 

    @endif 

    <!-- OPEN GRAPH META TAGS --> 
    <meta property="og:title" content="{{ $product->og_meta_title }}"> 

    <meta property="og:description" content="{{ $product->og_meta_description }}"> 

    <meta property="og:type" content="website"> 

    <meta property="og:url" content="{{ url()->current() }}"> 

    @if($product->og_meta_image) 
        <meta property="og:image" content="{{ asset('images/' . $product->og_meta_image) }}"> 
    @endif 
@endsection 



//OUTPUT:-
<img width="975" height="800" alt="image" src="https://github.com/user-attachments/assets/6a476bff-3531-438a-b4c8-8cdd3c207554" />


  





