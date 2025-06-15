<?php

use App\Http\Controllers\api\home\AdminCouponController;
use App\Http\Controllers\api\home\CartController;
use App\Http\Controllers\api\home\BuyerController;
use App\Http\Controllers\api\home\CategoryController;
use App\Http\Controllers\Api\home\FaqController;
use App\Http\Controllers\api\home\ProductController;
use App\Http\Controllers\api\home\SellerCouponController;
use App\Http\Controllers\Api\home\SliderController;
use App\Http\Controllers\Api\home\VendorController;
use App\Http\Controllers\Api\home\VendorRegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\home\BlogController;
use App\Http\Controllers\api\home\AboutController;
use App\Http\Controllers\api\home\AuthController;

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


    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/buyer/profile', [BuyerController::class, 'show']);
        Route::put('/buyer/profile', [BuyerController::class, 'update']);
        Route::put('/buyer/password', [BuyerController::class, 'changePassword']);

    });


    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);



    // “/api/admin/coupons” altında super-admin CRUD
    Route::middleware(['auth:sanctum', 'can:viewAny,App\Models\Coupon'])->prefix('admin')->group(function () {
        Route::apiResource('coupons', AdminCouponController::class);
        Route::post('coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])
            ->name('coupons.toggleActive');
    });

    // “/api/seller/coupons” altında saatıcının kendi kuponları
    Route::middleware(['auth:sanctum', 'can:viewAny,App\Models\Coupon'])->prefix('seller')->group(function () {
        Route::apiResource('coupons', SellerCouponController::class);
        Route::post('coupons/{coupon}/toggle-active', [SellerCouponController::class, 'toggleActive'])
            ->name('seller.coupons.toggleActive');
    });

});


