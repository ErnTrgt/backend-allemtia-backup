<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\CategoryRequest;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Slider;
use App\Models\Subcategory;
use App\Models\SubcategoryRequest;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Models\AboutSection;
use App\Models\MaintenanceSetting;
use Storage;
use PDF; // PDF kütüphanesi için

class AdminController extends Controller
{
    public function dashboard()
    {
        // Kullanıcı sayısını al
        $userCount = User::count();
        $orderCount = Order::count();
        $productCount = Product::count();

        // request
        $totalRequests = CategoryRequest::count();
        $pendingRequests = CategoryRequest::where('status', 'pending')->count();
        $approvedRequests = CategoryRequest::where('status', 'approved')->count();
        $rejectedRequests = CategoryRequest::where('status', 'rejected')->count();

        $totalCategories = Category::count();
        $totalSubcategories = Category::whereNotNull('parent_id')->count();
        $categoriesWithSubcategories = Category::whereNull('parent_id')->with('children')->get();
        // Role göre kullanıcı sayıları
        $adminCount = User::where('role', 'admin')->count();
        $sellerCount = User::where('role', 'seller')->count();
        $buyerCount = User::where('role', 'buyer')->count();

        // Store sayısı - eğer Store modeli yoksa seller sayısını kullan
        $totalStores = Store::count() ?: $sellerCount;
        
        // Additional data for modern dashboard
        $recentOrders = Order::with('items')->latest()->take(5)->get();
        $topSellers = User::where('role', 'seller')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();
        
        // Calculate today's revenue
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');
            
        // Calculate total revenue
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');

        // Bakım modu durumunu kontrol et
        try {
            $maintenanceMode = MaintenanceSetting::getActive();
        } catch (\Exception $e) {
            $maintenanceMode = null;
        }

        // Görünüme değişkeni aktar - Use modern dashboard
        return view('admin.dashboard-modern', compact(
            'userCount',
            'adminCount',
            'sellerCount',
            'buyerCount',
            'orderCount',
            'productCount',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'totalCategories',
            'totalSubcategories',
            'categoriesWithSubcategories',
            'totalStores',
            'recentOrders',
            'topSellers',
            'todayRevenue',
            'totalRevenue',
            'maintenanceMode'
        ));
    }

    public function userList(Request $request)
    {
        // Role göre filtreleme
        $role = $request->input('role'); // ?role=seller gibi bir parametre alır
        $users = $role ? User::where('role', $role)->get() : User::all();

        return view('admin.users-modern', compact('users', 'role'));
    }
    
    // AJAX User Store
    public function ajaxStoreUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:admin,seller,buyer',
            ], [
                'name.required' => 'Ad soyad alanı zorunludur.',
                'name.max' => 'Ad soyad en fazla 255 karakter olabilir.',
                'email.required' => 'E-posta adresi zorunludur.',
                'email.email' => 'Geçerli bir e-posta adresi giriniz.',
                'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
                'password.required' => 'Şifre alanı zorunludur.',
                'password.min' => 'Şifre en az 8 karakter olmalıdır.',
                'password.confirmed' => 'Şifreler eşleşmiyor.',
                'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
                'role.required' => 'Kullanıcı rolü seçilmelidir.',
                'role.in' => 'Geçersiz kullanıcı rolü.',
            ]);
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role' => $validated['role'],
                'status' => 'approved',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla eklendi!',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen formdaki hataları düzeltin.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Veritabanı hataları
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu bilgilerle kayıtlı bir veri zaten mevcut.',
                    'errors' => ['email' => ['Bu değer zaten kullanımda.']]
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Veritabanı hatası oluştu. Lütfen tekrar deneyin.'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Operation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Beklenmeyen bir hata oluştu. Lütfen sistem yöneticisine başvurun.'
            ], 500);
        }
    }
    
    // AJAX User Edit
    public function ajaxEditUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı!'
            ], 404);
        }
    }
    
    // AJAX User Update
    public function ajaxUpdateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:admin,seller,buyer',
                'status' => 'required|in:approved,pending',
            ], [
                'name.required' => 'Ad soyad alanı boş bırakılamaz.',
                'name.max' => 'Ad soyad en fazla 255 karakter olabilir.',
                'email.required' => 'E-posta adresi zorunludur.',
                'email.email' => 'Geçerli bir e-posta adresi giriniz.',
                'email.unique' => 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.',
                'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
                'role.required' => 'Kullanıcı rolü seçilmelidir.',
                'role.in' => 'Geçersiz kullanıcı rolü.',
                'status.required' => 'Kullanıcı durumu seçilmelidir.',
                'status.in' => 'Geçersiz kullanıcı durumu.',
            ]);
            
            $user->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla güncellendi!',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen formdaki hataları düzeltin.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Veritabanı hataları
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu bilgilerle kayıtlı bir veri zaten mevcut.',
                    'errors' => ['email' => ['Bu değer zaten kullanımda.']]
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Veritabanı hatası oluştu. Lütfen tekrar deneyin.'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Operation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Beklenmeyen bir hata oluştu. Lütfen sistem yöneticisine başvurun.'
            ], 500);
        }
    }
    
    // AJAX User Delete
    public function ajaxDeleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Admin kendini silemez
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendi hesabınızı silemezsiniz! Güvenlik nedeniyle bu işleme izin verilmiyor.'
                ], 403);
            }
            
            // Son admin kontrolü
            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Son yönetici hesabı silinemez! Sistemde en az bir yönetici olmalıdır.'
                    ], 403);
                }
            }
            
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla silindi!'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Foreign key constraint hatası
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kullanıcıya ait kayıtlar bulunuyor (siparişler, ürünler vb.). Önce bağlı kayıtları silmeniz gerekiyor.'
                ], 409);
            }
            return response()->json([
                'success' => false,
                'message' => 'Veritabanı hatası nedeniyle kullanıcı silinemedi.'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('User deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı silinirken beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    // AdminController.php - orders metodu güncellenmiş hali
    public function orderList(Request $request)
    {
        // POST request ve action update_status ise
        if ($request->isMethod('post') && $request->input('action') === 'update_status') {
            try {
                $request->validate([
                    'order_id' => 'required|exists:orders,id',
                    'status' => 'required|in:pending,waiting_payment,paid,processing,shipped,delivered,cancelled',
                    'tracking_number' => 'nullable|string|max:255',
                    'status_note' => 'nullable|string|max:500',
                    'cancel_reason' => 'nullable|string|max:500'
                ]);

                $order = Order::findOrFail($request->order_id);

                $updateData = [
                    'status' => $request->status,
                    'tracking_number' => $request->tracking_number,
                    'status_note' => $request->status_note
                ];

                // Eğer status cancelled ise
                if ($request->status === 'cancelled') {
                    $updateData['cancellation_reason'] = $request->cancel_reason;
                    $updateData['cancelled_at'] = now();
                    $updateData['is_partially_cancelled'] = false; // Tamamen iptal edildiği için kısmi iptal bayrağını kaldır
                }

                $order->update($updateData);

                return redirect()->back()->with('success', 'Order status updated successfully!');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error updating order: ' . $e->getMessage());
            }
        }

        // Normal orders listesi
        $query = Order::query();

        // Status filtresi
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Kısmi iptal filtresi
        if ($request->has('is_partially_cancelled')) {
            $query->where('is_partially_cancelled', (bool) $request->is_partially_cancelled);
        }

        // Satıcı filtresi
        if ($request->has('seller_id')) {
            $sellerId = $request->seller_id;
            $query->whereHas('items.product', function ($q) use ($sellerId) {
                $q->where('user_id', $sellerId);
            });
        }

        $orders = $query->with(['items.product.images', 'user'])->latest()->get();

        // Tüm satıcıları al
        $sellers = User::where('role', 'seller')->get();

        return view('admin.orders-modern', compact('orders', 'sellers'));
    }

    // AdminController.php - showOrder metodu
    public function showOrder($id)
    {
        $order = Order::with([
            'items.product.images',
            'items.product.user'
        ])->findOrFail($id);

        // Debug için
        // \Log::info('Order items:', $order->items->toArray());

        return view('admin.order-details-modern', compact('order'));
    }

    //tüm ürünleri listeleme
    public function productList(Request $request)
    {
        $sellerId = $request->input('seller_id'); // seller_id parametresi alınır
        $query = Product::query();

        if ($sellerId) {
            $query->where('user_id', $sellerId); // Belirli satıcının ürünleri filtrelenir
        }

        $products = $query->with(['images', 'user', 'category', 'store'])->latest()->get();
        $sellers = User::where('role', 'seller')->get(); // Tüm satıcıları al
        $categories = Category::all(); // Tüm kategorileri al
        $stores = Store::all(); // Tüm mağazaları al
        
        // İstatistikleri hesapla
        $stats = [
            'total' => $products->count(),
            'active' => $products->where('status', 'active')->count(),
            'pending' => $products->where('status', 'pending')->count(),
            'out_of_stock' => $products->where('stock', 0)->count(),
        ];

        return view('admin.products-modern', compact('products', 'sellers', 'sellerId', 'categories', 'stores', 'stats'));
    }

    // AJAX Product Store
    public function ajaxStoreProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'sku' => 'required|string|unique:products,sku',
                'image' => 'nullable|image|max:2048',
                'gallery_images.*' => 'nullable|image|max:2048',
                'status' => 'required|in:active,pending,inactive',
            ], [
                'name.required' => 'Ürün adı zorunludur.',
                'name.max' => 'Ürün adı en fazla 255 karakter olabilir.',
                'description.required' => 'Ürün açıklaması zorunludur.',
                'price.required' => 'Fiyat zorunludur.',
                'price.numeric' => 'Fiyat sayısal bir değer olmalıdır.',
                'price.min' => 'Fiyat 0\'dan küçük olamaz.',
                'discount_price.lt' => 'İndirimli fiyat normal fiyattan düşük olmalıdır.',
                'stock.required' => 'Stok miktarı zorunludur.',
                'stock.integer' => 'Stok miktarı tam sayı olmalıdır.',
                'stock.min' => 'Stok miktarı negatif olamaz.',
                'category_id.required' => 'Kategori seçimi zorunludur.',
                'category_id.exists' => 'Seçilen kategori geçerli değil.',
                'sku.required' => 'SKU kodu zorunludur.',
                'sku.unique' => 'Bu SKU kodu zaten kullanılıyor.',
                'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
                'image.max' => 'Resim boyutu 2MB\'dan küçük olmalıdır.',
            ]);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }
            
            // Add user_id (admin creating product)
            $validated['user_id'] = auth()->id();
            
            // Create product
            $product = Product::create($validated);
            
            // Handle gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $path = $image->store('products/gallery', 'public');
                    $product->images()->create(['image' => $path]);
                }
            }
            
            // Load relationships
            $product->load(['category', 'store', 'images']);
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla eklendi!',
                'product' => $product
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen formdaki hataları düzeltin.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Product creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ürün eklenirken bir hata oluştu.'
            ], 500);
        }
    }
    
    // AJAX Product Edit
    public function ajaxEditProduct($id)
    {
        try {
            $product = Product::with(['category', 'store', 'images'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı!'
            ], 404);
        }
    }
    
    // AJAX Product Update
    public function ajaxUpdateProduct(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'stock' => 'required|integer|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'sku' => 'nullable|string|unique:products,sku,' . $id,
                'image' => 'nullable|image|max:2048',
                'status' => 'nullable',
            ], [
                'name.required' => 'Ürün adı zorunludur.',
                'price.required' => 'Fiyat zorunludur.',
                'price.numeric' => 'Fiyat sayısal bir değer olmalıdır.',
                'discount_price.lt' => 'İndirimli fiyat normal fiyattan düşük olmalıdır.',
                'stock.required' => 'Stok miktarı zorunludur.',
            ]);
            
            // Convert status value to database format
            if (isset($validated['status'])) {
                $validated['status'] = ($validated['status'] == '1' || $validated['status'] == 'active' || $validated['status'] == 'Aktif') ? 'active' : 'inactive';
            }
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    \Storage::disk('public')->delete($product->image);
                }
                $validated['image'] = $request->file('image')->store('products', 'public');
            }
            
            $product->update($validated);
            $product->load(['category', 'store', 'images']);
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla güncellendi!',
                'product' => $product
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen formdaki hataları düzeltin.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Product update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ürün güncellenirken bir hata oluştu.'
            ], 500);
        }
    }
    
    // AJAX Product Delete
    public function ajaxDeleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete product images
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            
            // Delete gallery images
            foreach ($product->images as $image) {
                \Storage::disk('public')->delete($image->image);
            }
            
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla silindi!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Product deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ürün silinirken bir hata oluştu.'
            ], 500);
        }
    }

    //Belirli bir satıcının ürünlerini listeleme
    public function sellerProducts($id)
    {
        $seller = User::with('products')->where('id', $id)->whereHas('roles', function ($query) {
            $query->where('name', 'seller');
        })->firstOrFail();

        return view('admin.seller-products', compact('seller'));
    }

    public function approveUser($id)
    {
        $user = User::find($id);
        $user->update(['status' => 'approved']);
        return redirect()->route('admin.users')->with('success', 'User approved successfully.');
    }

    // GÜNCELLENMIŞ STORE LIST METODU
    public function storeList()
    {
        // Eğer ayrı bir stores tablosu varsa
        if (\Schema::hasTable('stores')) {
            $stores = Store::with('user')->get();

            // Eğer stores boşsa, seller'ları store olarak göster
            if ($stores->isEmpty()) {
                $stores = User::where('role', 'seller')
                    ->with(['products', 'products.images'])
                    ->withCount('products')
                    ->get();

                return view('admin.stores', [
                    'stores' => $stores,
                    'usingSellers' => true
                ]);
            }

            return view('admin.stores-modern', compact('stores'));
        }
        // Stores tablosu yoksa direkt seller'ları kullan
        else {
            $stores = User::where('role', 'seller')
                ->with(['products', 'products.images'])
                ->withCount('products')
                ->withCount([
                    'orders as total_sales' => function ($query) {
                        $query->where('status', 'delivered');
                    }
                ])
                ->withSum([
                    'orders as total_revenue' => function ($query) {
                        $query->where('status', 'delivered');
                    }
                ], 'total_amount')
                ->get();

            return view('admin.stores-modern', [
                'stores' => $stores,
                'usingSellers' => true
            ]);
        }
    }

    // Mağaza detayları görüntüleme
    public function showStore($id)
    {
        // Store modeli varsa
        if (\Schema::hasTable('stores')) {
            $store = Store::with(['user', 'products', 'products.images'])->findOrFail($id);
        } else {
            // Yoksa seller olarak bul
            $store = User::where('role', 'seller')
                ->with(['products', 'products.images'])
                ->withCount('products')
                ->withCount([
                    'orders as total_orders' => function ($query) {
                        $query->whereHas('items.product', function ($q) {
                            $q->where('user_id', $this->id);
                        });
                    }
                ])
                ->findOrFail($id);
        }

        // Mağaza istatistikleri
        $stats = [
            'total_products' => $store->products_count ?? $store->products->count(),
            'active_products' => $store->products->where('status', 1)->count(),
            'total_orders' => Order::whereHas('items.product', function ($query) use ($store) {
                $query->where('user_id', $store->id);
            })->count(),
            'pending_orders' => Order::whereHas('items.product', function ($query) use ($store) {
                $query->where('user_id', $store->id);
            })->where('status', 'pending')->count(),
            'total_revenue' => Order::whereHas('items.product', function ($query) use ($store) {
                $query->where('user_id', $store->id);
            })->where('status', 'delivered')->sum('total_amount'),
            'this_month_revenue' => Order::whereHas('items.product', function ($query) use ($store) {
                $query->where('user_id', $store->id);
            })->where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount')
        ];

        // Son siparişler
        $recentOrders = Order::whereHas('items.product', function ($query) use ($store) {
            $query->where('user_id', $store->id);
        })->latest()->take(10)->get();

        return view('admin.store-details-modern', compact('store', 'stats', 'recentOrders'));
    }

    // Mağaza durumunu değiştir
    public function toggleStoreStatus($id)
    {
        if (\Schema::hasTable('stores')) {
            $store = Store::findOrFail($id);
            $store->is_active = !$store->is_active;
            $store->save();

            // Store'un user'ını da güncelle
            if ($store->user) {
                $store->user->status = $store->is_active ? 'approved' : 'rejected';
                $store->user->save();
            }
        } else {
            $user = User::where('role', 'seller')->findOrFail($id);
            $user->status = $user->status === 'approved' ? 'rejected' : 'approved';
            $user->save();

            // Mağaza kapatıldıysa ürünleri de pasif yap
            if ($user->status === 'rejected') {
                Product::where('user_id', $user->id)->update(['status' => 0]);
            }
        }

        return redirect()->route('admin.stores')->with('success', 'Store status updated successfully.');
    }

    // Mağaza bilgilerini güncelle
    public function updateStore(Request $request, $id)
    {
        $request->validate([
            'store_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if (\Schema::hasTable('stores')) {
            $store = Store::findOrFail($id);
            $store->update($request->only(['store_name', 'description', 'commission_rate']));

            if ($store->user) {
                $store->user->status = $request->status;
                $store->user->save();
            }
        } else {
            $user = User::where('role', 'seller')->findOrFail($id);
            $user->status = $request->status;

            // Eğer user'da store bilgileri tutuluyorsa
            if ($request->filled('store_name')) {
                $user->store_name = $request->store_name;
            }
            if ($request->filled('description')) {
                $user->store_description = $request->description;
            }
            if ($request->filled('commission_rate')) {
                $user->commission_rate = $request->commission_rate;
            }

            $user->save();
        }

        return redirect()->route('admin.stores')->with('success', 'Store updated successfully.');
    }

    // Mağaza sil
    public function deleteStore($id)
    {
        if (\Schema::hasTable('stores')) {
            $store = Store::findOrFail($id);

            // Store'a ait ürünleri sil veya başka bir işlem yap
            Product::where('user_id', $store->user_id)->delete();

            $store->delete();
        } else {
            $user = User::where('role', 'seller')->findOrFail($id);

            // Kullanıcıya ait ürünleri sil
            Product::where('user_id', $user->id)->delete();

            // Kullanıcıyı sil
            $user->delete();
        }

        return redirect()->route('admin.stores')->with('success', 'Store deleted successfully.');
    }

    public function reports()
    {
        $orders = Order::with('user', 'items.product')->get();
        $totalRevenue = $orders->where('status', 'delivered')->sum('total_amount');

        // Aylık gelir
        $monthlyRevenue = Order::where('status', 'delivered')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // En çok satan ürünler
        $topProducts = Product::withCount([
            'orderItems as sold_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'delivered');
                });
            }
        ])->orderBy('sold_count', 'desc')->take(10)->get();

        // En çok satan mağazalar
        $topStores = User::where('role', 'seller')
            ->withSum([
                'products.orderItems as revenue' => function ($query) {
                    $query->whereHas('order', function ($q) {
                        $q->where('status', 'delivered');
                    });
                }
            ], 'total_price')
            ->orderBy('revenue', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports', compact('orders', 'totalRevenue', 'monthlyRevenue', 'topProducts', 'topStores'));
    }

    public function details($id)
    {
        $product = Product::with(['images', 'user', 'category'])->findOrFail($id);
        $categories = Category::all(); // Tüm kategorileri yükle
        return view('admin.product-details', compact('product', 'categories'));
    }

    // Route fonksiyonunu düzeltelim
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Validasyon
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'status' => 'nullable',
                'image' => 'nullable|image|max:2048',
                'images.*' => 'nullable|image|max:2048',
            ]);

            // Base data to update
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
            ];

            // Handle status field - database expects integer (1 or 0)
            if ($request->has('status')) {
                $updateData['status'] = (int)$request->status;
            }

            // Handle main image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && \Storage::exists('public/' . $product->image)) {
                    \Storage::delete('public/' . $product->image);
                }
                $updateData['image'] = $request->file('image')->store('products', 'public');
            }

            // Update product
            $product->update($updateData);

            // Handle deleted images
            if ($request->has('deleted_images') && !empty($request->deleted_images)) {
                $deletedImages = explode(',', $request->deleted_images);
                foreach ($deletedImages as $imageId) {
                    if ($imageId === 'main') {
                        // Delete main image
                        if ($product->image && \Storage::exists('public/' . $product->image)) {
                            \Storage::delete('public/' . $product->image);
                            $product->update(['image' => null]);
                        }
                    } else {
                        // Delete additional images
                        $image = ProductImage::where('product_id', $product->id)->where('id', $imageId)->first();
                        if ($image) {
                            if (\Storage::exists('public/' . $image->image_path)) {
                                \Storage::delete('public/' . $image->image_path);
                            }
                            $image->delete();
                        }
                    }
                }
            }

            // Handle additional images upload
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path
                    ]);
                }
            }

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ürün başarıyla güncellendi!',
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'status' => $product->status,
                        'category_id' => $product->category_id,
                        'description' => $product->description,
                    ]
                ]);
            }

            // Redirect to products page with success message
            return redirect()->route('admin.products')
                ->with('success', 'Ürün başarıyla güncellendi!');
                
        } catch (\Exception $e) {
            \Log::error('Product update error: ' . $e->getMessage());
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün güncellenirken bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Ürün güncellenirken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Profile işlemleri
    public function showProfile()
    {
        $admin = auth()->user(); // Giriş yapmış admin bilgilerini al
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:15',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $admin = auth()->user();
        
        // Avatar yükleme
        if ($request->hasFile('avatar')) {
            // Eski avatarı sil
            if ($admin->avatar && \Storage::disk('public')->exists($admin->avatar)) {
                \Storage::disk('public')->delete($admin->avatar);
            }
            
            // Yeni avatarı yükle
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $admin->avatar = $avatarPath;
        }
        
        // Diğer bilgileri güncelle
        $admin->update($request->except('avatar'));

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = !$product->status;
        $product->save();
        return redirect()->route('admin.products')->with('success', 'Product status updated successfully.');
    }
    
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'store_id' => 'required|exists:stores,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->all();
        $data['user_id'] = $request->store_id; // Store'un user_id'si
        $data['status'] = 'pending';
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }
        
        Product::create($data);
        
        return redirect()->route('admin.products')->with('success', 'Ürün başarıyla eklendi.');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Password updated successfully!');
    }

    // User sayfası için aşagısı
    public function changeUserStatus($id, $status)
    {
        $validStatuses = ['pending', 'approved', 'rejected'];

        if (!in_array($status, $validStatuses)) {
            // Check if it's an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status provided.'
                ], 400);
            }
            return redirect()->route('admin.users')->with('error', 'Invalid status provided.');
        }

        $user = User::findOrFail($id);
        $user->status = $status;
        $user->save();

        $message = "User status changed to " . ucfirst($status) . " successfully!";
        
        // Check if it's an AJAX request
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => $user
            ]);
        }
        
        return redirect()->route('admin.users')->with('success', $message);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:12',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,seller,buyer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'pending',
        ]);

        // Role ekle
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $user->assignRole($role);
        }

        return redirect()->route('admin.users')->with('success', 'User added successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:admin,seller,buyer',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }
    // User sayfası

    public function categoryRequests()
    {
        $requests = CategoryRequest::with('seller')->get();
        return view('admin.category-requests', compact('requests'));
    }

    public function updateCategoryRequest(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $categoryRequest = CategoryRequest::findOrFail($id);

        $categoryRequest->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            Category::create(['name' => $categoryRequest->name]);
        }

        return redirect()->route('admin.category.requests')->with('success', 'Category request updated successfully.');
    }

    public function subcategoryRequests()
    {
        $subcategoryRequests = SubcategoryRequest::with('seller', 'category')->get();

        return view('admin.subcategory-requests', compact('subcategoryRequests'));
    }

    public function updateSubcategoryRequestStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $subcategoryRequest = SubcategoryRequest::findOrFail($id);
        $subcategoryRequest->update(['status' => $request->status]);

        return redirect()->route('admin.subcategory-requests')->with('success', 'Subcategory request updated successfully!');
    }

    public function categories()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children.children'])
            ->withCount('products')
            ->get();
        $allCategories = Category::all(); // Alt kategori eklerken kullanmak için tüm kategoriler
        $totalCategories = Category::count();
        $totalSubcategories = Category::whereNotNull('parent_id')->count();
        
        // İstatistikler için ek veriler
        $categoryRequests = CategoryRequest::with('seller')->latest()->get();
        $pendingRequests = $categoryRequests->where('status', 'pending')->count();
        
        // En çok ürün olan kategori
        $topCategory = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->first();
            
        // Boş kategoriler
        $emptyCategories = Category::doesntHave('products')->count();
        
        // Son eklenen kategori
        $latestCategory = Category::latest()->first();
        
        return view('admin.categories-modern', compact(
            'categories', 
            'allCategories', 
            'totalCategories', 
            'totalSubcategories',
            'categoryRequests',
            'pendingRequests',
            'topCategory',
            'emptyCategories',
            'latestCategory'
        ));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        // Load relationships
        $category->load(['parent', 'children']);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla eklendi!',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Category added successfully!');
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->category_id,
        ]);

        // Load relationships
        $category->load(['parent', 'children']);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alt kategori başarıyla eklendi!',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Subcategory added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'children.*' => 'string|max:255',
            'grandchildren.*' => 'string|max:255',
        ]);

        // Parent ID belirleme - eğer parent_id gönderilmemişse mevcut parent_id'yi koru
        $newParentId = $request->has('parent_id') ? $request->parent_id : $category->parent_id;
        
        // Eğer keep_parent_id varsa ve parent_id yoksa, mevcut parent'ı koru
        if ($request->has('keep_parent_id') && !$request->has('parent_id')) {
            $newParentId = $category->parent_id;
        }
        
        // Döngüsel referans kontrolü (sadece parent değişiyorsa)
        if ($newParentId && $newParentId != $category->parent_id) {
            $parentCategory = Category::find($newParentId);
            if ($parentCategory && $parentCategory->isDescendantOf($category)) {
                return redirect()->back()->with('error', 'Bir kategori kendi alt kategorisinin altına taşınamaz!');
            }
            
            // 3 seviye kontrolü
            $level = 1;
            $checkParent = $parentCategory;
            while ($checkParent && $checkParent->parent_id) {
                $level++;
                $checkParent = $checkParent->parent;
            }
            
            // Eğer mevcut kategori alt kategorilere sahipse ve yeni parent 2. seviyeyse, izin verme
            if ($level >= 2 && $category->children()->exists()) {
                return redirect()->back()->with('error', 'Alt kategorileri olan bir kategori 3. seviyeye taşınamaz!');
            }
        }
        
        // Kategori adını güncelle, parent_id varsa onu da güncelle
        $updateData = ['name' => $request->name];
        
        // Sadece parent_id açıkça gönderilmişse güncelle
        if ($request->has('parent_id')) {
            $updateData['parent_id'] = $request->parent_id;
        }
        
        $category->update($updateData);

        // Alt kategorileri güncelle (eğer varsa)
        if ($request->has('children')) {
            foreach ($request->children as $childId => $childName) {
                $child = Category::findOrFail($childId);
                $child->update(['name' => $childName]);
            }
        }

        // Alt-alt kategorileri güncelle (eğer varsa)
        if ($request->has('grandchildren')) {
            foreach ($request->grandchildren as $grandchildId => $grandchildName) {
                $grandchild = Category::findOrFail($grandchildId);
                $grandchild->update(['name' => $grandchildName]);
            }
        }

        // Load relationships
        $category->load(['parent', 'children']);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla güncellendi!',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Kategori başarıyla güncellendi!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        // Alt kategorileri de sil veya ana kategoriye taşı
        foreach ($category->children as $child) {
            $child->delete(); // Ya da $child->update(['parent_id' => null]);
        }

        $category->delete();

        // If AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla silindi!'
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    // Get category stats for AJAX
    public function getCategoryStats()
    {
        $totalCategories = Category::count();
        $parentCategories = Category::whereNull('parent_id')->count();
        $subcategories = Category::whereNotNull('parent_id')->count();

        return response()->json([
            'total' => $totalCategories,
            'parents' => $parentCategories,
            'subcategories' => $subcategories
        ]);
    }

    /* ABOUT KISMI */
    public function aboutList()
    {
        $sections = AboutSection::paginate(10);
        return view('admin.about.index', compact('sections'));
    }

    public function editAboutSection($id)
    {
        $section = AboutSection::findOrFail($id);
        return view('admin.about.edit', compact('section'));
    }

    public function updateAboutSection(Request $request, $id)
    {
        $section = AboutSection::findOrFail($id);

        $request->validate([
            'section_key' => 'required|string|max:255|unique:about_sections,section_key,' . $id,
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean'
        ]);

        try {
            $section->section_key = $request->section_key;
            $section->title = $request->title;
            $section->content = $request->content;
            
            if ($request->has('status')) {
                $section->status = $request->status;
            }

            if ($request->hasFile('image')) {
                // Eski görseli sil
                if ($section->image && \Storage::disk('public')->exists($section->image)) {
                    \Storage::disk('public')->delete($section->image);
                }
                
                $path = $request->file('image')->store('about', 'public');
                $section->image = $path;
            }

            $section->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bölüm başarıyla güncellendi!',
                    'section' => [
                        'id' => $section->id,
                        'section_key' => $section->section_key,
                        'title' => $section->title,
                        'content' => $section->content,
                        'status' => $section->status,
                        'image_url' => $section->image ? asset('storage/' . $section->image) : null,
                        'updated_at' => $section->updated_at
                    ]
                ]);
            }

            return redirect()->route('admin.about.index')->with('success', 'Section updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bölüm güncellenirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Bölüm güncellenirken hata oluştu!');
        }
    }

    public function deleteAboutSection($id)
    {
        try {
            $section = AboutSection::findOrFail($id);

            // görseli de sil
            if ($section->image && \Storage::disk('public')->exists($section->image)) {
                \Storage::disk('public')->delete($section->image);
            }

            $section->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bölüm başarıyla silindi!'
                ]);
            }
            
            return redirect()->route('admin.about.index')->with('success', 'Section deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bölüm silinirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Bölüm silinirken hata oluştu!');
        }
    }

    public function storeAboutSection(Request $request)
    {
        $request->validate([
            'section_key' => 'required|string|max:255|unique:about_sections,section_key',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $about = new AboutSection();
            $about->section_key = $request->section_key;
            $about->title = $request->title;
            $about->content = $request->content;
            $about->status = $request->status ?? 1;

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('about', 'public');
                $about->image = $path;
            }

            $about->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bölüm başarıyla eklendi!',
                    'section' => [
                        'id' => $about->id,
                        'section_key' => $about->section_key,
                        'title' => $about->title,
                        'content' => $about->content,
                        'status' => $about->status,
                        'image_url' => $about->image ? asset('storage/' . $about->image) : null,
                        'created_at' => $about->created_at
                    ]
                ]);
            }

            return redirect()->route('admin.about.index')->with('success', 'New section added successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bölüm eklenirken hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Bölüm eklenirken hata oluştu!');
        }
    }

    public function toggleAboutStatus($id)
    {
        try {
            $section = AboutSection::findOrFail($id);
            $section->status = !$section->status;
            $section->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $section->status ? 'Bölüm aktifleştirildi!' : 'Bölüm pasifleştirildi!',
                    'section' => [
                        'id' => $section->id,
                        'section_key' => $section->section_key,
                        'title' => $section->title,
                        'content' => $section->content,
                        'status' => $section->status,
                        'image_url' => $section->image ? asset('storage/' . $section->image) : null
                    ]
                ]);
            }

            return redirect()->route('admin.about.index')->with('success', 'Section status updated.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durum güncellenirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Durum güncellenirken hata oluştu!');
        }
    }

    public function ajaxEditAboutSection($id)
    {
        try {
            $section = AboutSection::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'section' => [
                    'id' => $section->id,
                    'section_key' => $section->section_key,
                    'title' => $section->title,
                    'content' => $section->content,
                    'status' => $section->status,
                    'image_url' => $section->image ? asset('storage/' . $section->image) : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bölüm bulunamadı!'
            ], 404);
        }
    }

    public function getAboutStats()
    {
        $sections = AboutSection::all();
        
        return response()->json([
            'total' => $sections->count(),
            'active' => $sections->where('status', true)->count(),
            'inactive' => $sections->where('status', false)->count(),
            'with_images' => $sections->whereNotNull('image')->count()
        ]);
    }
    /* ABOUT KISMI */

    //FAQ
    public function faqList()
    {
        $faqs = Faq::paginate(10);
        return view('admin.faq.index-dynamic', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100'
        ]);

        try {
            $faq = Faq::create([
                'title' => $request->title,
                'content' => $request->content,
                'category' => $request->category ?: null,
                'status' => $request->status ?? 1
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'S.S.S başarıyla eklendi!',
                    'faq' => [
                        'id' => $faq->id,
                        'title' => $faq->title,
                        'content' => $faq->content,
                        'category' => $faq->category,
                        'status' => $faq->status
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'FAQ created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'S.S.S eklenirken hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'S.S.S eklenirken hata oluştu!');
        }
    }

    public function updateFaq(Request $request, $id)
    {
        try {
            $faq = Faq::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1'
            ]);

            $faq->title = $request->title;
            $faq->content = $request->content;
            $faq->category = $request->category ?: null;
            
            if ($request->has('status')) {
                $faq->status = (int)$request->status;
            }
            
            $faq->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'S.S.S başarıyla güncellendi!',
                    'faq' => [
                        'id' => $faq->id,
                        'title' => $faq->title,
                        'content' => $faq->content,
                        'category' => $faq->category,
                        'status' => $faq->status
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'FAQ updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('FAQ Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'S.S.S güncellenirken hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'S.S.S güncellenirken hata oluştu!');
        }
    }

    public function deleteFaq($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'S.S.S başarıyla silindi!'
                ]);
            }
            
            return redirect()->back()->with('success', 'FAQ deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'S.S.S silinirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'S.S.S silinirken hata oluştu!');
        }
    }

    public function toggleFaqStatus($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->status = !$faq->status;
            $faq->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $faq->status ? 'S.S.S aktifleştirildi!' : 'S.S.S pasifleştirildi!',
                    'faq' => [
                        'id' => $faq->id,
                        'title' => $faq->title,
                        'content' => $faq->content,
                        'category' => $faq->category,
                        'status' => $faq->status
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'FAQ status updated.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durum güncellenirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Durum güncellenirken hata oluştu!');
        }
    }

    public function ajaxEditFaq($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'faq' => [
                    'id' => $faq->id,
                    'title' => $faq->title,
                    'content' => $faq->content,
                    'category' => $faq->category,
                    'status' => $faq->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'S.S.S bulunamadı!'
            ], 404);
        }
    }

    public function getFaqStats()
    {
        $faqs = Faq::all();
        
        return response()->json([
            'total' => $faqs->count(),
            'active' => $faqs->where('status', true)->count(),
            'inactive' => $faqs->where('status', false)->count(),
            'categories' => $faqs->pluck('category')->filter()->unique()->count()
        ]);
    }
    //FAQ

    //  SLIDER
    public function sliderList()
    {
        $sliders = Slider::paginate(10);
        return view('admin.slider.index', compact('sliders'));
    }

    public function storeSlider(Request $request)
    {
        $request->validate([
            'tag_one' => 'nullable|string',
            'tag_two' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // 5MB
        ]);

        $data = $request->only('tag_one', 'tag_two', 'description');
        $data['status'] = $request->status ?? 1;
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider = Slider::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Slider başarıyla eklendi!',
                'slider' => [
                    'id' => $slider->id,
                    'tag_one' => $slider->tag_one,
                    'tag_two' => $slider->tag_two,
                    'description' => $slider->description,
                    'status' => $slider->status,
                    'image_url' => $slider->image ? asset('storage/' . $slider->image) : null,
                    'created_at' => $slider->created_at
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Slider added successfully.');
    }

    public function updateSlider(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $request->validate([
            'tag_one' => 'nullable|string',
            'tag_two' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // 5MB
            'status' => 'nullable|boolean'
        ]);

        $data = $request->only('tag_one', 'tag_two', 'description');
        
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        if ($request->hasFile('image')) {
            if ($slider->image && \Storage::exists('public/' . $slider->image)) {
                \Storage::delete('public/' . $slider->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Slider başarıyla güncellendi!',
                'slider' => [
                    'id' => $slider->id,
                    'tag_one' => $slider->tag_one,
                    'tag_two' => $slider->tag_two,
                    'description' => $slider->description,
                    'status' => $slider->status,
                    'image_url' => $slider->image ? asset('storage/' . $slider->image) : null,
                    'updated_at' => $slider->updated_at
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Slider updated.');
    }

    public function deleteSlider($id)
    {
        try {
            $slider = Slider::findOrFail($id);

            if ($slider->image && \Storage::exists('public/' . $slider->image)) {
                \Storage::delete('public/' . $slider->image);
            }

            $slider->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slider başarıyla silindi!'
                ]);
            }

            return redirect()->back()->with('success', 'Slider deleted.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slider silinirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Slider silinirken hata oluştu!');
        }
    }

    public function toggleSliderStatus($id)
    {
        try {
            $slider = Slider::findOrFail($id);
            $slider->status = !$slider->status;
            $slider->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $slider->status ? 'Slider aktifleştirildi!' : 'Slider pasifleştirildi!',
                    'slider' => [
                        'id' => $slider->id,
                        'tag_one' => $slider->tag_one,
                        'tag_two' => $slider->tag_two,
                        'description' => $slider->description,
                        'status' => $slider->status,
                        'image_url' => $slider->image ? asset('storage/' . $slider->image) : null
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Slider status updated.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durum güncellenirken hata oluştu!'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Durum güncellenirken hata oluştu!');
        }
    }

    public function ajaxEditSlider($id)
    {
        try {
            $slider = Slider::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'slider' => [
                    'id' => $slider->id,
                    'tag_one' => $slider->tag_one,
                    'tag_two' => $slider->tag_two,
                    'description' => $slider->description,
                    'status' => $slider->status,
                    'image_url' => $slider->image ? asset('storage/' . $slider->image) : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider bulunamadı!'
            ], 404);
        }
    }

    public function getSliderStats()
    {
        $sliders = Slider::all();
        
        return response()->json([
            'total' => $sliders->count(),
            'active' => $sliders->where('status', true)->count(),
            'inactive' => $sliders->where('status', false)->count(),
            'with_images' => $sliders->whereNotNull('image')->count()
        ]);
    }
    //  SLIDER

    /**
     * KUPON
     */
    public function index()
    {
        $products = Product::all();
        $coupons = Coupon::with('products')->paginate(10);
        return view('admin.coupons.index', compact('coupons', 'products'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.coupons.create', compact('products'));
    }

    public function store(Request $r)
    {
        try {
            $data = $r->validate([
                'code' => 'required|string|unique:coupons,code',
                'type' => 'required|in:fixed,percent,free_shipping',
                'value' => 'nullable|numeric',
                'min_order_amount' => 'nullable|numeric',
                'usage_limit' => 'nullable|integer',
                'once_per_user' => 'boolean',
                'starts_at' => 'nullable|date',
                'expires_at' => 'nullable|date',
                'active' => 'boolean',
                'product_ids' => 'array'
            ]);
            
            $data['seller_id'] = null; // süper admin
            $data['used_count'] = 0; // Initialize used_count
            $coupon = Coupon::create($data);
            
            if (!empty($data['product_ids'])) {
                $coupon->products()->sync($data['product_ids']);
            }
            
            // AJAX request kontrolü
            if ($r->ajax() || $r->wantsJson()) {
                $coupon->load('products');
                return response()->json([
                    'success' => true,
                    'message' => 'Kupon başarıyla oluşturuldu!',
                    'coupon' => $coupon
                ]);
            }
            
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon created');
        } catch (\Exception $e) {
            if ($r->ajax() || $r->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon oluşturulurken hata oluştu: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::all();
        return view('admin.coupons.edit', compact('coupon', 'products'));
    }
    
    public function ajaxEditCoupon($id)
    {
        try {
            $coupon = Coupon::with('products')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'coupon' => $coupon
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon bulunamadı!'
            ], 404);
        }
    }

    public function couponupdate(Request $r, Coupon $coupon)
    {
        try {
            $data = $r->validate([
                'code' => "required|string|unique:coupons,code,{$coupon->id}",
                'type' => 'required|in:fixed,percent,free_shipping',
                'value' => 'nullable|numeric',
                'min_order_amount' => 'nullable|numeric',
                'usage_limit' => 'nullable|integer',
                'once_per_user' => 'boolean',
                'starts_at' => 'nullable|date',
                'expires_at' => 'nullable|date',
                'active' => 'boolean',
                'product_ids' => 'array'
            ]);
            
            $coupon->update($data);
            $coupon->products()->sync($data['product_ids'] ?? []);
            
            // AJAX request kontrolü
            if ($r->ajax() || $r->wantsJson()) {
                $coupon->load('products');
                return response()->json([
                    'success' => true,
                    'message' => 'Kupon başarıyla güncellendi!',
                    'coupon' => $coupon
                ]);
            }
            
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated');
        } catch (\Exception $e) {
            if ($r->ajax() || $r->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon güncellenirken hata oluştu: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
            
            // AJAX request kontrolü
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kupon başarıyla silindi!'
                ]);
            }
            
            return back()->with('success', 'Deleted');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon silinirken hata oluştu: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function toggle(Coupon $coupon)
    {
        try {
            $coupon->active = !$coupon->active;
            $coupon->save();
            
            // AJAX request kontrolü
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $coupon->active ? 'Kupon aktifleştirildi!' : 'Kupon pasifleştirildi!',
                    'coupon' => $coupon
                ]);
            }
            
            return back()->with('success', 'Status toggled');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon durumu değiştirilirken hata oluştu: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function getCouponStats()
    {
        try {
            $coupons = Coupon::all();
            
            $stats = [
                'total' => $coupons->count(),
                'active' => $coupons->where('active', true)->count(),
                'used' => $coupons->sum('used_count'),
                'expired' => $coupons->filter(function($c) { 
                    return $c->expires_at && $c->expires_at->isPast(); 
                })->count()
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler yüklenirken hata oluştu'
            ], 500);
        }
    }
    
    /**
     * KUPON
     */

    //ORDER UPDATe
    public function updateOrderStatus(Request $request)
    {
        try {
            \Log::info('Update order status request:', $request->all()); // Debug için

            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:pending,waiting_payment,paid,processing,shipped,delivered,cancelled',
                'tracking_number' => 'nullable|string|max:255',
                'status_note' => 'nullable|string|max:500',
                'cancel_reason' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($request->order_id);

            $updateData = [
                'status' => $request->status,
                'tracking_number' => $request->tracking_number,
                'status_note' => $request->status_note
            ];

            // Eğer status cancelled ise cancel reason'ı da ekle
            if ($request->status === 'cancelled') {
                $updateData['cancellation_reason'] = $request->cancel_reason;
                $updateData['cancelled_at'] = now();
            }

            $order->update($updateData);

            \Log::info('Order updated successfully:', ['order_id' => $order->id, 'new_status' => $order->status]);

            // Load relationships for complete data
            $order->load(['items']);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'tracking_number' => $order->tracking_number,
                    'total' => $order->total,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'cancellation_reason' => $order->cancellation_reason
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating order status:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    }

    //Order İptal Etme
    public function deleteOrder($id)
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->status === 'processing' || $order->status === 'shipped') {
                return response()->json([
                    'success' => false,
                    'message' => 'Active orders cannot be deleted'
                ], 400);
            }

            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Sipariş iptal etme
    public function cancelOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->input('reason', 'Admin tarafından iptal edildi'),
                'cancelled_at' => now()
            ]);
            
            return redirect()->back()->with('success', 'Sipariş başarıyla iptal edildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sipariş iptal edilirken bir hata oluştu.');
        }
    }
    
    // Order item iptal etme metodu
    public function cancelOrderItem(Request $request, $orderId, $itemId)
    {
        try {
            $request->validate([
                'cancel_reason' => 'required|string|max:500',
                'return_to_stock' => 'nullable|boolean'
            ]);

            $order = Order::findOrFail($orderId);
            $orderItem = $order->items()->findOrFail($itemId);

            // Ürün zaten iptal edilmiş mi kontrol et
            if ($orderItem->is_cancelled) {
                return redirect()->back()->with('error', 'Bu ürün zaten iptal edilmiş.');
            }

            // Sipariş iptale uygun mu kontrol et
            if (in_array($order->status, ['delivered', 'cancelled'])) {
                return redirect()->back()->with('error', 'Bu sipariş durumunda ürün iptali yapılamaz.');
            }

            // Ürünü iptal et
            $orderItem->update([
                'is_cancelled' => true,
                'cancel_reason' => $request->cancel_reason,
                'cancelled_at' => now()
            ]);

            // Stok iade işlemi
            if ($request->has('return_to_stock') && $request->return_to_stock) {
                if ($orderItem->product) {
                    $orderItem->product->increment('stock', $orderItem->quantity);
                }
            }

            // Siparişin tüm ürünleri iptal edilmiş mi kontrol et
            $allItemsCancelled = $order->items()->where('is_cancelled', false)->count() === 0;

            // Tüm ürünler iptal edilmişse, siparişi tamamen iptal et
            if ($allItemsCancelled) {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Tüm ürünler iptal edildi',
                    'cancelled_at' => now()
                ]);
            }
            // Kısmi iptal
            else {
                $order->update([
                    'is_partially_cancelled' => true
                ]);
            }

            // Toplam tutarı güncelle
            $totals = $order->updateTotalAmount();
            \Log::info('Admin: Order totals after cancellation', $totals);

            return redirect()->back()->with('success', 'Ürün başarıyla iptal edildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ürün iptal edilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Blog listesi
     */
    public function blogindex()
    {
        if (request()->ajax()) {
            $blogs = Blog::latest();

            return DataTables::of($blogs)
                ->addIndexColumn()
                ->addColumn('image', function ($blog) {
                    if ($blog->blog_img) {
                        $url = asset('storage/' . $blog->blog_img);
                        return '<img src="' . $url . '" width="80" height="60" style="object-fit: cover;">';
                    }
                    return '<span class="text-muted">No image</span>';
                })
                ->addColumn('status', function ($blog) {
                    $checked = $blog->status ? 'checked' : '';
                    return '<div class="form-check form-switch">
                        <input class="form-check-input change-status" type="checkbox" 
                            data-id="' . $blog->id . '" ' . $checked . '>
                    </div>';
                })
                ->addColumn('action', function ($blog) {
                    return '
                        <div class="btn-group" role="group">
                            <a href="' . route('admin.blogs.edit', $blog->id) . '" 
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger delete-blog" 
                                data-id="' . $blog->id . '">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('date', function ($blog) {
                    return $blog->date->format('d.m.Y');
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $blogs = Blog::latest()->get();
        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Yeni blog formu
     */
    public function blogcreate()
    {
        return view('admin.blogs.create');
    }

    /**
     * Yeni blog kaydet
     */
    public function blogstore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'content' => 'required|string',
                'author' => 'required|string|max:255',
                'date' => 'required|date',
                'blog_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'boolean'
            ]);

            $data = $request->all();

            // Görsel yükleme
            if ($request->hasFile('blog_img')) {
                $imagePath = $request->file('blog_img')->store('blogs', 'public');
                $data['blog_img'] = $imagePath;
            }

            // views alanını başlat
            $data['views'] = 0;

            $blog = Blog::create($data);

            // AJAX request kontrolü
            if ($request->ajax() || $request->wantsJson()) {
                $blog->blog_img_url = $blog->blog_img ? asset('storage/' . $blog->blog_img) : null;
                return response()->json([
                    'success' => true,
                    'message' => 'Blog başarıyla eklendi!',
                    'blog' => $blog
                ]);
            }

            return redirect()->route('admin.blogs.index')
                ->with('success', 'Blog başarıyla eklendi.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog eklenirken hata oluştu: ' . $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Blog düzenleme formu
     */
    public function blogedit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }
    
    /**
     * Blog bilgilerini AJAX ile getir
     */
    public function ajaxEditBlog($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->blog_img_url = $blog->blog_img ? asset('storage/' . $blog->blog_img) : null;
            
            return response()->json([
                'success' => true,
                'blog' => $blog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog bulunamadı!'
            ], 404);
        }
    }
    
    /**
     * Blog istatistiklerini getir
     */
    public function getBlogStats()
    {
        try {
            $blogs = Blog::all();
            
            $stats = [
                'total' => $blogs->count(),
                'active' => $blogs->where('status', true)->count(),
                'inactive' => $blogs->where('status', false)->count(),
                'views' => $blogs->sum('views')
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler yüklenirken hata oluştu'
            ], 500);
        }
    }

    /**
     * Blog güncelle
     */
    public function blogupdate(Request $request, Blog $blog)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'content' => 'required|string',
                'author' => 'required|string|max:255',
                'date' => 'required|date',
                'blog_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'boolean'
            ]);

            $data = $request->all();

            // Görsel yükleme
            if ($request->hasFile('blog_img')) {
                // Eski görseli sil
                if ($blog->blog_img) {
                    Storage::disk('public')->delete($blog->blog_img);
                }

                $imagePath = $request->file('blog_img')->store('blogs', 'public');
                $data['blog_img'] = $imagePath;
            }

            $blog->update($data);

            // AJAX request kontrolü
            if ($request->ajax() || $request->wantsJson()) {
                $blog->blog_img_url = $blog->blog_img ? asset('storage/' . $blog->blog_img) : null;
                return response()->json([
                    'success' => true,
                    'message' => 'Blog başarıyla güncellendi!',
                    'blog' => $blog
                ]);
            }

            return redirect()->route('admin.blogs.index')
                ->with('success', 'Blog başarıyla güncellendi.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog güncellenirken hata oluştu: ' . $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Blog sil
     */
    public function blogdestroy(Blog $blog)
    {
        try {
            // Görseli sil
            if ($blog->blog_img) {
                Storage::disk('public')->delete($blog->blog_img);
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog başarıyla silindi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Blog durumunu değiştir
     */
    public function changeStatus(Request $request)
    {
        try {
            $blog = Blog::find($request->id);
            if ($blog) {
                $blog->status = $request->status;
                $blog->save();
                
                // AJAX request kontrolü
                if ($request->ajax() || $request->wantsJson()) {
                    $blog->blog_img_url = $blog->blog_img ? asset('storage/' . $blog->blog_img) : null;
                    return response()->json([
                        'success' => true,
                        'message' => $blog->status ? 'Blog yayınlandı!' : 'Blog taslağa alındı!',
                        'blog' => $blog
                    ]);
                }

                return redirect()->back()->with('success', 'Blog durumu başarıyla güncellendi.');
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog bulunamadı!'
                ], 404);
            }

            return redirect()->back()->with('error', 'Blog bulunamadı.');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'İşlem sırasında bir hata oluştu.');
        }
    }

    /**
     * Sipariş faturasını PDF olarak oluşturur ve görüntüler
     * 
     * @param int $order Sipariş ID
     * @return \Illuminate\Http\Response
     */
    public function invoice($order)
    {
        try {
            // Sipariş bilgilerini al
            $order = Order::with([
                'items.product.images',
                'items.product.user'
            ])->findOrFail($order);

            // İptal edilen ürünlerin toplamını hesapla
            $cancelledTotal = $order->items->where('is_cancelled', true)->sum('subtotal');
            // Güncel toplam tutarı hesapla
            $currentTotal = $order->total - $cancelledTotal;

            // Sipariş notlarını hazırla
            $orderNotes = [
                'customer_note' => $order->notes,
                'status_note' => $order->status_note,
                'cancellation_reason' => $order->cancellation_reason,
                'seller_note' => $order->seller_note
            ];

            // Takip bilgilerini hazırla
            $trackingInfo = null;
            if ($order->tracking_number) {
                $trackingInfo = [
                    'number' => $order->tracking_number,
                    'status' => $order->status
                ];
            }

            // PDF oluştur
            $pdf = PDF::loadView('admin.invoice', compact('order', 'cancelledTotal', 'currentTotal', 'orderNotes', 'trackingInfo'));

            // PDF ayarlarını yapılandır
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
                'isPhpEnabled' => true,
                'isJavascriptEnabled' => true,
                'chroot' => public_path(),
            ]);

            // Header ve footer için inline HTML kullanımı
            $header = view('admin.invoice-header')->render();
            $footer = view('admin.invoice-footer', compact('order', 'trackingInfo'))->render();

            // Header ve footer'ı PDF'e ekle
            $pdf->setOption('header-html', $header);
            $pdf->setOption('footer-html', $footer);
            $pdf->setOption('margin-top', 30);
            $pdf->setOption('margin-bottom', 25);
            $pdf->setOption('margin-left', 15);
            $pdf->setOption('margin-right', 15);
            $pdf->setOption('encoding', 'UTF-8');

            // PDF'i görüntüle
            return $pdf->stream("Fatura-{$order->order_number}.pdf");

        } catch (\Exception $e) {
            \Log::error('Fatura oluşturma hatası:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Fatura oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Belirli bir satıcıya ait sipariş ürünlerinin faturasını PDF olarak oluşturur
     * 
     * @param int $orderId Sipariş ID
     * @param int $sellerId Satıcı ID
     * @return \Illuminate\Http\Response
     */
    public function sellerInvoice($orderId, $sellerId)
    {
        try {
            // Siparişi ve satıcıya ait ürünleri getir
            $order = Order::with(['items.product.images', 'items.product.user'])
                ->whereHas('items.product', function ($q) use ($sellerId) {
                    $q->where('user_id', $sellerId);
                })
                ->findOrFail($orderId);

            // Satıcıyı bul
            $seller = User::where('role', 'seller')->findOrFail($sellerId);

            // Sadece bu satıcıya ait ürünleri filtrele
            $sellerItems = $order->items->filter(function ($item) use ($sellerId) {
                return $item->product && $item->product->user_id == $sellerId;
            });

            // İptal edilen ürünlerin toplamını hesapla
            $cancelledTotal = $sellerItems->where('is_cancelled', true)->sum('subtotal');

            // Güncel toplam tutarı hesapla
            $sellerTotal = $sellerItems->sum('subtotal');
            $currentTotal = $sellerTotal - $cancelledTotal;

            // Sipariş notlarını hazırla
            $orderNotes = [
                'customer_note' => $order->notes,
                'status_note' => $order->status_note,
                'cancellation_reason' => $order->cancellation_reason,
                'seller_note' => $order->seller_note
            ];

            // Takip bilgilerini hazırla
            $trackingInfo = null;
            if ($order->tracking_number) {
                $trackingInfo = [
                    'number' => $order->tracking_number,
                    'status' => $order->status
                ];
            }

            // PDF oluştur - satıcı faturası şablonunu kullan
            $pdf = PDF::loadView('admin.seller-invoice', compact('order', 'seller', 'sellerItems', 'cancelledTotal', 'currentTotal', 'sellerTotal', 'orderNotes', 'trackingInfo'));

            // PDF ayarlarını yapılandır
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
                'isPhpEnabled' => true,
                'isJavascriptEnabled' => true,
                'chroot' => public_path(),
            ]);

            // Header ve footer için inline HTML kullanımı
            $header = view('admin.invoice-header')->render();
            $footer = view('admin.invoice-footer', compact('order', 'trackingInfo'))->render();

            // Header ve footer'ı PDF'e ekle
            $pdf->setOption('header-html', $header);
            $pdf->setOption('footer-html', $footer);
            $pdf->setOption('margin-top', 30);
            $pdf->setOption('margin-bottom', 25);
            $pdf->setOption('margin-left', 15);
            $pdf->setOption('margin-right', 15);
            $pdf->setOption('encoding', 'UTF-8');

            // PDF'i görüntüle
            return $pdf->stream("Fatura-{$order->order_number}-{$seller->name}.pdf");

        } catch (\Exception $e) {
            \Log::error('Satıcı faturası oluşturma hatası:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Fatura oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }


}