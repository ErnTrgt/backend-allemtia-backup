<?php

use App\Http\Controllers\api\home\CategoryController;
use App\Http\Controllers\Api\home\FaqController;
use App\Http\Controllers\api\home\ProductController;
use App\Http\Controllers\Api\home\SliderController;
use App\Http\Controllers\Api\home\VendorController;
use App\Http\Controllers\Api\home\VendorRegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\home\BlogController;
use App\Http\Controllers\api\home\AboutController;

Route::group(['prefix' => 'home', 'as' => 'home.'], function () {
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/category/{categoryId}', [ProductController::class, 'getProductsByCategory'])->name('products');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('about', [AboutController::class, 'index']);
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::get('/sliders', [SliderController::class, 'index']);

    Route::get('blogs', [BlogController::class, 'index'])->name('blogs'); // Bloglar için endpoint
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');

    Route::post('/register-vendor', [VendorRegisterController::class, 'store']);
    Route::get('/vendors', [VendorController::class, 'index']);
    Route::get('/vendors/{id}', [VendorController::class, 'show']);
    // routes/api.php
    Route::get('/vendors/{id}/products', [VendorController::class, 'products']);

});


