<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        ));

        $totalUsers = User::count();
        $totalStores = Store::count();
        $totalOrders = Order::count();
        return view('admin.dashboard', compact('totalUsers', 'totalStores', 'totalOrders'));
    }

    public function userList(Request $request)
    {
        // Role göre filtreleme
        $role = $request->input('role'); // ?role=seller gibi bir parametre alır
        $users = $role ? User::where('role', $role)->get() : User::all();


        return view('admin.users', compact('users', 'role'));
    }
    public function orderList()
    {
        // Örnek: Sipariş modelini kullanarak tüm siparişleri çekin
        $orders = Order::latest()->get(); // Eğer Order modeliniz yoksa oluşturun
        return view('admin.orders', compact('orders'));
    }
    public function showOrder($id)
    {
        $order = Order::findOrFail($id); // Eğer Order modeliniz yoksa oluşturun
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

    public function storeList()
    {
        $stores = Store::with('user')->get();
        return view('admin.stores', compact('stores'));
    }

    public function reports()
    {
        $orders = Order::with('user', 'product')->get();
        $totalRevenue = $orders->sum('total_price');
        return view('admin.reports', compact('orders', 'totalRevenue'));
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

    //Slider
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


    /**
     * Kupon listesini gösterir
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


}
