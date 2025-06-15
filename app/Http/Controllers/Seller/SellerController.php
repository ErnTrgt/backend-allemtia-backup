<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryRequest;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductImage;
use App\Models\SubcategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
class SellerController extends Controller
{
    public function dashboard()
    {

        // Satıcının kategori taleplerini al
        $pendingRequests = CategoryRequest::where('seller_id', auth()->id())
            ->where('status', 'pending')
            ->count();

        $approvedRequests = CategoryRequest::where('seller_id', auth()->id())
            ->where('status', 'approved')
            ->count();

        $rejectedRequests = CategoryRequest::where('seller_id', auth()->id())
            ->where('status', 'rejected')
            ->count();


        // Satıcıya ait ürünlerin sayısı
        $productCount = Product::where('user_id', auth()->id())->count(); // seller_id yerine id
        $orderCount = Order::where('id', auth()->id())->count(); // seller_id yerine id

        return view('seller.dashboard', compact(
            'productCount',
            'orderCount',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests'
        ));
    }

    public function products()
    {
        // Satıcıya ait ürünler
        //$stores = Store::where('user_id', auth()->id())->get(); // Sadece giriş yapan kullanıcıya ait mağazalar

        $products = Product::where('user_id', auth()->id())->get();
        $categories = Category::all(); // Kategorileri al
        return view('seller.products', compact('products', 'categories'));
    }

    public function orders()
    {
        // Satıcıya ait siparişler
        $orders = Order::where('id', auth()->id())->get(); // seller_id yerine id
        return view('seller.orders', compact('orders'));
    }

    public function profile()
    {
        $products = Product::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();

        // Giriş yapan kullanıcının bilgilerini ve ürünlerini profile.blade.php'ye gönderiyoruz
        return view('seller.profile', [
            'user' => auth()->user(),
            'products' => $products
        ]);
    }
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'country' => $request->input('country'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }


    public function changePassword()
    {
        return view('seller.change-password');
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Kullanıcının şifresini güncelle
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    public function productDetails($id)
    {
        $product = Product::with(['images', 'user'])->findOrFail($id);
        $categories = Category::with('children')->get(); // Tüm kategoriler ve alt kategoriler

        // Giriş yapmış kullanıcının son eklediği 3 ürünü al
        $recentProducts = Product::where('user_id', Auth::id()) // Kullanıcının ürünleri
            ->latest() // En son eklenen
            ->take(3) // İlk 3 ürünYYYYYY
            ->get();
        return view('seller.product-details', compact('product', 'recentProducts', 'categories'));
    }

    // ürün ekleme  işlemleri burada
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id', // Kategori doğrulama
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'category_id' => $request->category_id, // Kategori ilişkilendirme
            'user_id' => auth()->id(),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return redirect()->route('seller.products')->with('success', 'Product added successfully!');
    }

    // Ürün Güncelleme İşlemi burada
    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id', // Kategori doğrulama
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product->update($request->only(['name', 'price', 'stock', 'description', 'category_id']));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return redirect()->route('seller.products')->with('success', 'Product updated successfully!');
    }
    // Ürün aktifleme pasifleme
    public function toggleStatus($id)
    {
        $product = Product::where('user_id', auth()->id())->findOrFail($id);
        $product->update(['status' => !$product->status]);

        $message = $product->status ? 'Product activated successfully!' : 'Product deactivated successfully!';
        return redirect()->route('seller.products')->with('success', $message);
    }
    public function categoryRequests()
    {
        $requests = CategoryRequest::where('seller_id', auth()->id())->get();
        return view('seller.category-requests', compact('requests'));
    }

    public function storeCategoryRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CategoryRequest::create([
            'seller_id' => auth()->id(),
            'name' => $request->name,
        ]);

        return redirect()->route('seller.category.requests')->with('success', 'Category request submitted successfully.');
    }
    public function subcategoryRequests()
    {
        $categories = Category::all();
        $subcategoryRequests = SubcategoryRequest::where('seller_id', auth()->id())->get();

        return view('seller.subcategory-requests', compact('categories', 'subcategoryRequests'));
    }

    public function storeSubcategoryRequest(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_name' => 'required|string|max:255',
        ]);

        SubcategoryRequest::create([
            'seller_id' => auth()->id(),
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
        ]);

        return redirect()->route('seller.subcategory-requests')->with('success', 'Subcategory request submitted successfully!');
    }


    //coupon 

    /**
     * Satıcının kendi kuponlarını listeler.
     */
    public function index()
    {
        $products = Product::all();

        $sellerId = Auth::id();
        $coupons = Coupon::where('seller_id', $sellerId)->with('products')->get();
        return view('seller.coupons.index', compact('coupons', 'products'));
    }
    public function create()
    {
        $products = Product::where('user_id', Auth::id())->get();
        return view('seller.coupons.create', compact('products'));
    }
    public function store(Request $r)
    {
        $data = $r->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric',
            'min_order_amount' => 'nullable|numeric',
            'usage_limit' => 'nullable|integer',
            'expires_at' => 'nullable|date',
            'active' => 'required|boolean',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        $data['seller_id'] = Auth::id();
        $coupon = Coupon::create([
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['value'] ?? 0,
            'min_order_amount' => $data['min_order_amount'],
            'usage_limit' => $data['usage_limit'],
            'expires_at' => $data['expires_at'],
            'active' => $data['active'],
            'seller_id' => auth()->id(),
        ]);

        // 3) Eğer ürün ilişkisi varsa pivot tablosuna eşle
        if (!empty($data['product_ids'])) {
            $coupon->products()->sync($data['product_ids']);
        }
        //$coupon->products()->sync($data['product_ids'] ?? []);
        return redirect()->route('seller.coupons.index')->with('success', 'Coupon created');
    }
    public function edit(Coupon $coupon)
    {
        // $this->authorize('update',$coupon); // politikalarla kontrol edebilirsiniz
        $products = Product::where('user_id', Auth::id())->get();
        return view('seller.coupons.edit', compact('coupon', 'products'));
    }
    public function couponupdate(Request $r, Coupon $coupon)
    {
        // $this->authorize('update',$coupon);
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
        return back()->with('success', 'Updated');
    }
    public function destroy(Coupon $coupon)
    {
        // $this->authorize('delete',$coupon);
        $coupon->delete();
        return back()->with('success', 'Deleted');
    }
    public function toggle(Coupon $coupon)
    {
        // $this->authorize('update',$coupon);
        $coupon->active = !$coupon->active;
        $coupon->save();
        return back()->with('success', 'Toggled');
    }

}
