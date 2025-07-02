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
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Models\AboutSection;
use Storage;

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
        $totalSubcategories = Subcategory::count();
        $categoriesWithSubcategories = Category::with('subcategories')->get();
        // Role göre kullanıcı sayıları
        $adminCount = User::where('role', 'admin')->count();
        $sellerCount = User::where('role', 'seller')->count();
        $buyerCount = User::where('role', 'buyer')->count();

        // Store sayısı - eğer Store modeli yoksa seller sayısını kullan
        $totalStores = Store::count() ?: $sellerCount;

        // Görünüme değişkeni aktar
        return view('admin.dashboard', compact(
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
            'totalStores'
        ));
    }

    public function userList(Request $request)
    {
        // Role göre filtreleme
        $role = $request->input('role'); // ?role=seller gibi bir parametre alır
        $users = $role ? User::where('role', $role)->get() : User::all();

        return view('admin.users', compact('users', 'role'));
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
                }

                $order->update($updateData);

                return redirect()->back()->with('success', 'Order status updated successfully!');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error updating order: ' . $e->getMessage());
            }
        }

        // Normal orders listesi
        $orders = Order::latest()->get();

        return view('admin.orders', compact('orders'));
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

        return view('admin.order-details', compact('order'));
    }

    //tüm ürünleri listeleme
    public function productList(Request $request)
    {
        $sellerId = $request->input('seller_id'); // seller_id parametresi alınır
        $query = Product::query();

        if ($sellerId) {
            $query->where('user_id', $sellerId); // Belirli satıcının ürünleri filtrelenir
        }

        $products = $query->with(['images', 'user'])->latest()->get();
        $sellers = User::where('role', 'seller')->get(); // Tüm satıcıları al

        return view('admin.products', compact('products', 'sellers', 'sellerId'));
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

            return view('admin.stores', compact('stores'));
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

            return view('admin.stores', [
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

        return view('admin.store-details', compact('store', 'stats', 'recentOrders'));
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
        $product = Product::with(['images', 'user'])->findOrFail($id);
        return view('admin.product-details', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Tüm alanları güncelle
        $product->update($request->except('images'));

        // Yeni görselleri yükle
        if ($request->hasFile('images')) {
            // Mevcut görselleri isteğe bağlı olarak silebilirsiniz
            foreach ($product->images as $image) {
                if (\Storage::exists('public/' . $image->image_path)) {
                    \Storage::delete('public/' . $image->image_path);
                }
                $image->delete();
            }

            // Yeni görselleri yükle ve kaydet
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.products')->with('success', 'Product updated successfully with images.');
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
        ]);

        $admin = auth()->user();
        $admin->update($request->all());

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = !$product->status;
        $product->save();
        return redirect()->route('admin.products')->with('success', 'Product status updated successfully.');
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
            return redirect()->route('admin.users')->with('error', 'Invalid status provided.');
        }

        $user = User::findOrFail($id);
        $user->status = $status;
        $user->save();

        $message = "User status changed to " . ucfirst($status) . " successfully!";
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
        $categories = Category::with('subcategories')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create(['name' => $request->name]);

        return redirect()->route('admin.categories')->with('success', 'Category added successfully!');
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Subcategory added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'subcategories.*' => 'string|max:255',
        ]);

        $category->update(['name' => $request->name]);

        if ($request->has('subcategories')) {
            foreach ($request->subcategories as $subcategoryId => $subcategoryName) {
                $subcategory = Subcategory::findOrFail($subcategoryId);
                $subcategory->update(['name' => $subcategoryName]);
            }
        }

        return redirect()->route('admin.categories')->with('success', 'Category and subcategories updated successfully!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    /* ABOUT KISMI */
    public function aboutList()
    {
        $sections = AboutSection::all();
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
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('about', 'public');
            $section->image = $path;
        }

        $section->title = $request->title;
        $section->content = $request->content;
        $section->save();

        return redirect()->route('admin.about.index')->with('success', 'Section updated successfully!');
    }

    public function deleteAboutSection($id)
    {
        $section = AboutSection::findOrFail($id);

        // görseli de sil
        if ($section->image && \Storage::disk('public')->exists($section->image)) {
            \Storage::disk('public')->delete($section->image);
        }

        $section->delete();
        return redirect()->route('admin.about.index')->with('success', 'Section deleted successfully.');
    }

    public function storeAboutSection(Request $request)
    {
        $request->validate([
            'section_key' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $about = new AboutSection();
        $about->section_key = $request->section_key;
        $about->title = $request->title;
        $about->content = $request->content;

        if ($request->hasFile('image')) {
            try {
                $path = $request->file('image')->store('about', 'public');
                $about->image = $path;
            } catch (\Exception $e) {
                dd('Görsel yükleme hatası: ' . $e->getMessage());
            }
        }

        $about->save(); // KAYIT BURADA

        return redirect()->route('admin.about.index')->with('success', 'New section added successfully!');
    }

    public function toggleAboutStatus($id)
    {
        $section = AboutSection::findOrFail($id);
        $section->status = !$section->status;
        $section->save();

        return redirect()->route('admin.about.index')->with('success', 'Section status updated.');
    }
    /* ABOUT KISMI */

    //FAQ
    public function faqList()
    {
        $faqs = Faq::all();
        return view('admin.faq.index', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        Faq::create($request->only('title', 'content') + ['status' => 1]);

        return redirect()->back()->with('success', 'FAQ created successfully.');
    }

    public function updateFaq(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $faq->update($request->only('title', 'content'));

        return redirect()->back()->with('success', 'FAQ updated successfully.');
    }

    public function deleteFaq($id)
    {
        Faq::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'FAQ deleted successfully.');
    }

    public function toggleFaqStatus($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->status = !$faq->status;
        $faq->save();

        return redirect()->back()->with('success', 'FAQ status updated.');
    }
    //FAQ

    //  SLIDER
    public function sliderList()
    {
        $sliders = Slider::all();
        return view('admin.slider.index', compact('sliders'));
    }

    public function storeSlider(Request $request)
    {
        $request->validate([
            'tag_one' => 'nullable|string',
            'tag_two' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('tag_one', 'tag_two', 'description');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        Slider::create($data + ['status' => 1]);

        return redirect()->back()->with('success', 'Slider added successfully.');
    }

    public function updateSlider(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $data = $request->only('tag_one', 'tag_two', 'description');

        if ($request->hasFile('image')) {
            if ($slider->image && \Storage::exists('public/' . $slider->image)) {
                \Storage::delete('public/' . $slider->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        return redirect()->back()->with('success', 'Slider updated.');
    }

    public function deleteSlider($id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider->image && \Storage::exists('public/' . $slider->image)) {
            \Storage::delete('public/' . $slider->image);
        }

        $slider->delete();

        return redirect()->back()->with('success', 'Slider deleted.');
    }

    public function toggleSliderStatus($id)
    {
        $slider = Slider::findOrFail($id);
        $slider->status = !$slider->status;
        $slider->save();

        return redirect()->back()->with('success', 'Slider status updated.');
    }
    //  SLIDER

    /**
     * KUPON
     */
    public function index()
    {
        $products = Product::all();
        $coupons = Coupon::with('products')->get();
        return view('admin.coupons.index', compact('coupons', 'products'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.coupons.create', compact('products'));
    }

    public function store(Request $r)
    {
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
        $coupon = Coupon::create($data);
        if (!empty($data['product_ids'])) {
            $coupon->products()->sync($data['product_ids']);
        }
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created');
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::all();
        return view('admin.coupons.edit', compact('coupon', 'products'));
    }

    public function couponupdate(Request $r, Coupon $coupon)
    {
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
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Deleted');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->active = !$coupon->active;
        $coupon->save();
        return back()->with('success', 'Status toggled');
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

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
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

        return view('admin.blogs.index');
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'date' => 'required|date',
            'blog_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = $request->all();

        // Görsel yükleme
        if ($request->hasFile('blog_img')) {
            $imagePath = $request->file('blog_img')->store('blogs', 'public');
            $data['blog_img'] = $imagePath;
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog başarıyla eklendi.');
    }

    /**
     * Blog düzenleme formu
     */
    public function blogedit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    /**
     * Blog güncelle
     */
    public function blogupdate(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'date' => 'required|date',
            'blog_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
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

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog başarıyla güncellendi.');
    }

    /**
     * Blog sil
     */
    public function blogdestroy(Blog $blog)
    {
        // Görseli sil
        if ($blog->blog_img) {
            Storage::disk('public')->delete($blog->blog_img);
        }

        $blog->delete();

        return response()->json(['success' => true]);
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

                return redirect()->back()->with('success', 'Blog durumu başarıyla güncellendi.');
            }

            return redirect()->back()->with('error', 'Blog bulunamadı.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'İşlem sırasında bir hata oluştu.');
        }
    }




}