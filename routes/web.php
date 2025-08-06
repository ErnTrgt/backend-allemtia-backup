<?php


use App\Http\Controllers\api\home\SellerCouponController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\SellerAuthController;
// use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Seller\SellerController;

// ===========================
//      AUTHENTICATION ROUTES
// ===========================
// Varsayılan "login" rotasını tanımlayın.
Route::get('/login', function () {
    return redirect()->route('seller.login');
})->name('login');



// Seller login/logout routes
Route::get('/seller/login', [SellerAuthController::class, 'showLoginForm'])->name('seller.login');
Route::post('/seller/login', [SellerAuthController::class, 'login'])->name('seller.login.submit');
Route::post('/seller/logout', [SellerAuthController::class, 'logout'])->name('seller.logout');

// Admin login/logout routes
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ===========================
//      ADMIN PANEL ROUTES
// ===========================
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'userList'])->name('admin.users');
    Route::put('/admin/users/{id}/change-status/{status}', [AdminController::class, 'changeUserStatus'])->name('admin.users.changeStatus');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');

    // Blog yönetimi - name() kullanmadan
    Route::get('/admin/blogs', [AdminController::class, 'blogindex'])->name('admin.blogs.index');
    Route::get('/admin/blogs/create', [AdminController::class, 'blogcreate'])->name('admin.blogs.create');
    Route::post('/admin/blogs', [AdminController::class, 'blogstore'])->name('admin.blogs.store');
    Route::get('/admin/blogs/{blog}/edit', [AdminController::class, 'blogedit'])->name('admin.blogs.edit');
    Route::put('/admin/blogs/{blog}', [AdminController::class, 'blogupdate'])->name('admin.blogs.update');
    Route::delete('/admin/blogs/{blog}', [AdminController::class, 'blogdestroy'])->name('admin.blogs.destroy');
    Route::post('/admin/blogs/change-status', [AdminController::class, 'changeStatus'])->name('admin.blogs.change-status');

    //order management
    Route::match(['GET', 'POST'], '/admin/orders', [AdminController::class, 'orderList'])->name('admin.orders');
    Route::get('/admin/orders/{id}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/admin/orders/{order}/items/{item}/cancel', [AdminController::class, 'cancelOrderItem'])->name('admin.orders.cancel_item');
    Route::get('/admin/products', [AdminController::class, 'productList'])->name('admin.products');
    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/admin/product/{id}', [AdminController::class, 'details'])->name('admin.product.details');
    Route::put('/admin/product/{id}', [AdminController::class, 'update'])->name('admin.product.update');
    Route::put('/admin/product/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.product.toggleStatus');
    Route::delete('/admin/product/{id}', [AdminController::class, 'delete'])->name('admin.product.delete');
    Route::get('/admin/products/export', [AdminController::class, 'exportProducts'])->name('admin.products.export');
    Route::put('/admin/orders/{order}/cancel', [AdminController::class, 'cancelOrder'])->name('admin.orders.cancel');
    Route::get('/admin/orders/{order}/invoice', [AdminController::class, 'invoice'])->name('admin.orders.invoice');

    //Route::get('/orders/{order}/invoice', [AdminController::class, 'invoice'])->name('admin.orders.invoice');
    Route::delete('/orders/{order}', [AdminController::class, 'destroy'])->name('admin.orders.delete');
    Route::put('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');
    Route::put('/admin/orders/update', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');
    Route::get('/admin/orders/{order}/seller/{seller}/invoice', [AdminController::class, 'sellerInvoice'])->name('admin.orders.seller.invoice');


    Route::get('/admin/seller/{id}/products', [AdminController::class, 'sellerProducts'])->name('admin.seller.products');
    Route::post('/admin/users/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::get('/admin/stores', [AdminController::class, 'storeList'])->name('admin.stores');
    Route::post('/admin/stores', [AdminController::class, 'store'])->name('admin.stores.store');
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.resetPassword');

    // Category and Subcategory management
    Route::get('/admin/category-requests', [AdminController::class, 'categoryRequests'])->name('admin.category.requests');
    Route::put('/admin/category-requests/{id}', [AdminController::class, 'updateCategoryRequest'])->name('admin.category.requests.update');
    Route::get('/admin/subcategory-requests', [AdminController::class, 'subcategoryRequests'])->name('admin.subcategory.requests');
    Route::put('/admin/subcategory-requests/{id}', [AdminController::class, 'updateSubcategoryRequestStatus'])->name('admin.updateSubcategoryRequestStatus');
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::post('/admin/subcategories', [AdminController::class, 'storeSubcategory'])->name('admin.subcategories.store');
    Route::put('/admin/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.updateCategory');
    Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.deleteCategory');

    //ABOUT
    Route::get('/admin/about', [AdminController::class, 'aboutList'])->name('admin.about.index');
    Route::get('/admin/about/{id}/edit', [AdminController::class, 'editAboutSection'])->name('admin.about.edit');
    Route::post('/admin/about/{id}', [AdminController::class, 'updateAboutSection'])->name('admin.about.update');
    Route::post('/about/store', [AdminController::class, 'storeAboutSection'])->name('admin.about.store');
    Route::put('/admin/about/{id}/toggle-status', [AdminController::class, 'toggleAboutStatus'])->name('admin.about.toggleStatus');
    Route::delete('/admin/about/{id}', [AdminController::class, 'deleteAboutSection'])->name('admin.about.delete');

    //FAQ 
    Route::get('/admin/faqs', [AdminController::class, 'faqList'])->name('admin.faq.index');
    Route::post('/admin/faqs/store', [AdminController::class, 'storeFaq'])->name('admin.faq.store');
    Route::put('/admin/faqs/{id}', [AdminController::class, 'updateFaq'])->name('admin.faq.update');
    Route::delete('/admin/faqs/{id}', [AdminController::class, 'deleteFaq'])->name('admin.faq.delete');
    Route::put('/admin/faqs/{id}/toggle', [AdminController::class, 'toggleFaqStatus'])->name('admin.faq.toggle');

    // Slider
    Route::get('/admin/sliders', [AdminController::class, 'sliderList'])->name('admin.slider.index');
    Route::post('/admin/sliders/store', [AdminController::class, 'storeSlider'])->name('admin.slider.store');
    Route::put('/admin/sliders/{id}', [AdminController::class, 'updateSlider'])->name('admin.slider.update');
    Route::delete('/admin/sliders/{id}', [AdminController::class, 'deleteSlider'])->name('admin.slider.delete');
    Route::put('/admin/sliders/{id}/toggle', [AdminController::class, 'toggleSliderStatus'])->name('admin.slider.toggle');
    // Admin profile
    Route::get('/admin/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');


    // Store Management Routes
    Route::get('/store/{id}', [AdminController::class, 'showStore'])->name('admin.store.show');
    Route::post('/store/{id}/toggle', [AdminController::class, 'toggleStoreStatus'])->name('admin.store.toggle');
    Route::put('/store/{id}', [AdminController::class, 'updateStore'])->name('admin.store.update');
    Route::delete('/store/{id}', [AdminController::class, 'deleteStore'])->name('admin.store.delete');


    // Coupon Yönetim Sayfası
    Route::get('/admin/coupons', [AdminController::class, 'index'])->name('admin.coupons.index');
    Route::get('/admin/coupons/create', [AdminController::class, 'create'])->name('admin.coupons.create');
    Route::post('/admin/coupons', [AdminController::class, 'store'])->name('admin.coupons.store');
    Route::get('/admin/coupons/{coupon}/edit', [AdminController::class, 'edit'])->name('admin.coupons.edit');
    Route::put('/admin/coupons/{coupon}', [AdminController::class, 'couponupdate'])->name('admin.coupons.update');
    Route::delete('/admin/coupons/{coupon}', [AdminController::class, 'destroy'])->name('admin.coupons.destroy');
    Route::put('/admin/coupons/{coupon}/toggle', [AdminController::class, 'toggle'])
        ->name('admin.coupons.toggle');

    // Bakım Modu Yönetimi
    Route::get('/admin/maintenance', [MaintenanceController::class, 'index'])->name('admin.maintenance.index');
    Route::post('/admin/maintenance/toggle', [MaintenanceController::class, 'toggle'])->name('admin.maintenance.toggle');
    Route::put('/admin/maintenance/update', [MaintenanceController::class, 'update'])->name('admin.maintenance.update');
    Route::get('/admin/maintenance/status', [MaintenanceController::class, 'status'])->name('admin.maintenance.status');
});

// ===========================
//      SELLER PANEL ROUTES
// ===========================
Route::middleware(['auth:seller'])->group(function () {
    Route::get('/seller/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    Route::get('/seller/products', [SellerController::class, 'products'])->name('seller.products');
    Route::post('/seller/products/store', [SellerController::class, 'storeProduct'])->name('seller.products.store');
    Route::get('/seller/products/{id}/details', [SellerController::class, 'productDetails'])->name('seller.products.details');
    Route::put('/seller/products/{id}/update', [SellerController::class, 'updateProduct'])->name('seller.products.update');
    Route::put('/seller/products/{id}/toggle-status', [SellerController::class, 'toggleStatus'])->name('seller.products.toggleStatus');

    Route::get('/seller/orders', [SellerController::class, 'orders'])->name('seller.orders');
    Route::get('/seller/orders/{order}/print-items', [SellerController::class, 'printOrderItems'])->name('orders.print');

    // Sepette unutulan ürünler için mail gönderme
    Route::post('/seller/abandoned-cart/send-emails', [SellerController::class, 'sendAbandonedCartEmails'])->name('abandoned-cart.send-emails');

    // Sepet ve Favoriler
    Route::get('/seller/cart-items', [SellerController::class, 'cartItems'])->name('seller.cart-items');
    Route::get('/seller/wishlist-items', [SellerController::class, 'wishlistItems'])->name('seller.wishlist-items');

    // Seller Profile & Settings
    Route::get('/seller/profile', [SellerController::class, 'profile'])->name('seller.profile');
    Route::post('/seller/profile/update', [SellerController::class, 'updateProfile'])->name('seller.profile.update');
    Route::get('/seller/password-change', [SellerController::class, 'changePassword'])->name('seller.password.change');
    Route::post('/seller/password-update', [SellerController::class, 'updatePassword'])->name('seller.password.update');
    Route::post('/seller/upload-avatar', [SellerController::class, 'uploadAvatar'])->name('seller.avatar.upload');

    // Category Requests
    Route::get('/seller/category-requests', [SellerController::class, 'categoryRequests'])->name('seller.category.requests');
    Route::post('/seller/category-requests', [SellerController::class, 'storeCategoryRequest'])->name('seller.category.requests.store');

    // Subcategory Requests
    Route::get('/seller/subcategory-requests', [SellerController::class, 'subcategoryRequests'])->name('seller.subcategory.requests');
    Route::post('/seller/subcategory-requests', [SellerController::class, 'storeSubcategoryRequest'])->name('seller.storeSubcategoryRequest');

    // routes/web.php - Seller routes kısmına
    Route::put('/seller/orders/{order}/status', [SellerController::class, 'updateOrderStatus'])->name('seller.orders.updateStatus');
    Route::post('/seller/orders/{order}/cancel-item/{item}', [SellerController::class, 'cancelOrderItem'])->name('seller.orders.cancel_item');

    // Kupon CRUD
    // Coupon Yönetim Sayfası
    Route::get('/seller/coupons', [SellerController::class, 'index'])->name('seller.coupons.index');
    Route::get('/seller/coupons/create', [SellerController::class, 'create'])->name('seller.coupons.create');
    Route::post('/seller/coupons', [SellerController::class, 'store'])->name('seller.coupons.store');
    Route::get('/seller/coupons/{coupon}/edit', [SellerController::class, 'edit'])->name('seller.coupons.edit');
    Route::put('/seller/coupons/{coupon}', [SellerController::class, 'couponupdate'])->name('seller.coupons.update');
    Route::delete('/seller/coupons/{coupon}', [SellerController::class, 'destroy'])->name('seller.coupons.destroy');
    Route::put('/seller/coupons/{coupon}/toggle', [SellerController::class, 'toggle'])
        ->name('seller.coupons.toggle');

});

// ===========================
//      PUBLIC ROUTES
// ===========================
Route::get('/', function () {
    return view('welcome');
});