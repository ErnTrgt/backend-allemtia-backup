<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryRequest;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductImage;
use App\Models\SubcategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Validator;
use Log;
use Storage;
use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Added for sendAbandonedCartEmails
use Illuminate\Support\Facades\Mail; // Added for sendAbandonedCartEmails
use App\Mail\AbandonedCartReminder; // Added for sendAbandonedCartEmails

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
        $productCount = Product::where('user_id', auth()->id())->count();

        // Satıcının siparişlerini al
        $orderCount = Order::whereHas('items.product', function ($q) {
            $q->where('user_id', auth()->id());
        })->count();

        // Aktif siparişleri al (tamamlanmamış ve iptal edilmemiş)
        $activeOrdersCount = Order::whereHas('items.product', function ($q) {
            $q->where('user_id', auth()->id());
        })->whereNotIn('status', ['delivered', 'cancelled'])->count();

        // Aktif kuponları al
        $couponCount = Coupon::where('seller_id', auth()->id())
            ->where('active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })->count();

        // Düşük stoklu ürünleri al (stok 10'dan az)
        $lowStockProducts = Product::where('user_id', auth()->id())
            ->where('stock', '<', 10)
            ->where('stock', '>', 0)
            ->with('images')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Son 5 siparişi al
        $recentOrders = Order::whereHas('items.product', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();

        // Satış istatistikleri için veri hazırlama
        $salesData = $this->prepareSalesData();

        // Detaylı satış istatistikleri
        $salesStats = $this->getDetailedSalesStats();

        // Sepete eklenen ürünler
        $cartItems = Cart::whereHas('product', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->with(['product.images', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Favorilere eklenen ürünler
        $wishlistItems = Wishlist::whereHas('product', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->with(['product.images', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Sepete en çok eklenen ürünler
        $topCartProducts = Cart::select('product_id', \DB::raw('COUNT(*) as cart_count'))
            ->whereHas('product', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->groupBy('product_id')
            ->orderByDesc('cart_count')
            ->with('product.images')
            ->take(3)
            ->get();

        // Favorilere en çok eklenen ürünler
        $topWishlistProducts = Wishlist::select('product_id', \DB::raw('COUNT(*) as wishlist_count'))
            ->whereHas('product', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->groupBy('product_id')
            ->orderByDesc('wishlist_count')
            ->with('product.images')
            ->take(3)
            ->get();

        return view('seller.dashboard', compact(
            'productCount',
            'orderCount',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'activeOrdersCount',
            'couponCount',
            'lowStockProducts',
            'recentOrders',
            'salesData',
            'salesStats',
            'cartItems',
            'wishlistItems',
            'topCartProducts',
            'topWishlistProducts'
        ));
    }

    /**
     * Satış istatistikleri için veri hazırlar
     * @return array
     */
    private function prepareSalesData()
    {
        try {
            $sellerId = auth()->id();

            // Haftalık satış verileri (son 7 gün)
            $weeklyData = [];
            $weeklyLabels = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $weeklyLabels[] = $date->locale('tr')->dayName;

                try {
                    $dailySales = Order::whereDate('created_at', $date->format('Y-m-d'))
                        ->whereHas('items.product', function ($query) use ($sellerId) {
                            $query->where('user_id', $sellerId);
                        })
                        ->whereNotIn('status', ['cancelled'])
                        ->with([
                            'items' => function ($query) use ($sellerId) {
                                $query->whereHas('product', function ($q) use ($sellerId) {
                                    $q->where('user_id', $sellerId);
                                })->where('is_cancelled', false);
                            }
                        ])
                        ->get()
                        ->sum(function ($order) {
                            return $order->items->sum('subtotal');
                        });

                    $weeklyData[] = round($dailySales, 2);
                } catch (\Exception $e) {
                    \Log::error('Haftalık satış verisi hesaplanırken hata: ' . $e->getMessage());
                    $weeklyData[] = 0;
                }
            }

            // Aylık satış verileri (son 12 ay)
            $monthlyData = [];
            $monthlyLabels = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthlyLabels[] = $date->locale('tr')->monthName;

                try {
                    $monthlySales = Order::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->whereHas('items.product', function ($query) use ($sellerId) {
                            $query->where('user_id', $sellerId);
                        })
                        ->whereNotIn('status', ['cancelled'])
                        ->with([
                            'items' => function ($query) use ($sellerId) {
                                $query->whereHas('product', function ($q) use ($sellerId) {
                                    $q->where('user_id', $sellerId);
                                })->where('is_cancelled', false);
                            }
                        ])
                        ->get()
                        ->sum(function ($order) {
                            return $order->items->sum('subtotal');
                        });

                    $monthlyData[] = round($monthlySales, 2);
                } catch (\Exception $e) {
                    \Log::error('Aylık satış verisi hesaplanırken hata: ' . $e->getMessage());
                    $monthlyData[] = 0;
                }
            }

            // Yıllık satış verileri (son 5 yıl)
            $yearlyData = [];
            $yearlyLabels = [];

            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i)->year;
                $yearlyLabels[] = (string) $year;

                try {
                    $yearlySales = Order::whereYear('created_at', $year)
                        ->whereHas('items.product', function ($query) use ($sellerId) {
                            $query->where('user_id', $sellerId);
                        })
                        ->whereNotIn('status', ['cancelled'])
                        ->with([
                            'items' => function ($query) use ($sellerId) {
                                $query->whereHas('product', function ($q) use ($sellerId) {
                                    $q->where('user_id', $sellerId);
                                })->where('is_cancelled', false);
                            }
                        ])
                        ->get()
                        ->sum(function ($order) {
                            return $order->items->sum('subtotal');
                        });

                    $yearlyData[] = round($yearlySales, 2);
                } catch (\Exception $e) {
                    \Log::error('Yıllık satış verisi hesaplanırken hata: ' . $e->getMessage());
                    $yearlyData[] = 0;
                }
            }

            // Log verileri
            \Log::info('Satış verileri hazırlandı', [
                'weekly' => [
                    'data' => $weeklyData,
                    'labels' => $weeklyLabels
                ],
                'monthly' => [
                    'data' => $monthlyData,
                    'labels' => $monthlyLabels
                ],
                'yearly' => [
                    'data' => $yearlyData,
                    'labels' => $yearlyLabels
                ]
            ]);

            return [
                'weekly' => [
                    'data' => $weeklyData,
                    'labels' => $weeklyLabels
                ],
                'monthly' => [
                    'data' => $monthlyData,
                    'labels' => $monthlyLabels
                ],
                'yearly' => [
                    'data' => $yearlyData,
                    'labels' => $yearlyLabels
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Satış verileri hazırlanırken genel hata: ' . $e->getMessage());

            // Hata durumunda örnek veriler döndür
            return [
                'weekly' => [
                    'data' => [4500, 5200, 3800, 6700, 4900, 7800, 8500],
                    'labels' => ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar']
                ],
                'monthly' => [
                    'data' => [18500, 21300, 25600, 19200, 28700, 32500, 38400, 35200, 29700, 42100, 38900, 47500],
                    'labels' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık']
                ],
                'yearly' => [
                    'data' => [245000, 312000, 387000, 452000, 528000],
                    'labels' => ['2019', '2020', '2021', '2022', '2023']
                ]
            ];
        }
    }

    /**
     * Detaylı satış istatistikleri
     * @return array
     */
    private function getDetailedSalesStats()
    {
        $sellerId = auth()->id();

        // Toplam satış tutarı (tamamlanmış siparişler)
        $totalRevenue = Order::whereHas('items.product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })
            ->whereIn('status', ['delivered', 'shipped', 'processing', 'paid'])
            ->with([
                'items' => function ($query) use ($sellerId) {
                    $query->whereHas('product', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })->where('is_cancelled', false);
                }
            ])
            ->get()
            ->sum(function ($order) {
                return $order->items->sum('subtotal');
            });

        // İptal edilen tutar
        $cancelledRevenue = Order::whereHas('items.product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })
            ->with([
                'items' => function ($query) use ($sellerId) {
                    $query->whereHas('product', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })->where('is_cancelled', true);
                }
            ])
            ->get()
            ->sum(function ($order) {
                return $order->items->sum('subtotal');
            });

        // En çok satılan 3 ürün
        $bestSellingProducts = OrderItem::whereHas('product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })
            ->where('is_cancelled', false)
            ->select('product_id', \DB::raw('SUM(quantity) as total_quantity'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->with('product.images')
            ->take(3)
            ->get();

        // En az satılan 3 ürün (en az 1 kez satılmış)
        $leastSellingProducts = OrderItem::whereHas('product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })
            ->where('is_cancelled', false)
            ->select('product_id', \DB::raw('SUM(quantity) as total_quantity'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'asc')
            ->with('product.images')
            ->take(3)
            ->get();

        // Ortalama sipariş değeri
        $avgOrderValue = 0;
        $orderCount = Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('user_id', $sellerId);
        })->count();

        if ($orderCount > 0) {
            $avgOrderValue = $totalRevenue / $orderCount;
        }

        // Bu ayın satışları
        $thisMonthRevenue = Order::whereHas('items.product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })
            ->whereIn('status', ['delivered', 'shipped', 'processing', 'paid'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->with([
                'items' => function ($query) use ($sellerId) {
                    $query->whereHas('product', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })->where('is_cancelled', false);
                }
            ])
            ->get()
            ->sum(function ($order) {
                return $order->items->sum('subtotal');
            });

        // Geçen aya göre değişim
        $lastMonthRevenue = Order::whereHas('items.product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })
            ->whereIn('status', ['delivered', 'shipped', 'processing', 'paid'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->with([
                'items' => function ($query) use ($sellerId) {
                    $query->whereHas('product', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })->where('is_cancelled', false);
                }
            ])
            ->get()
            ->sum(function ($order) {
                return $order->items->sum('subtotal');
            });

        $monthlyGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $monthlyGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        return [
            'totalRevenue' => round($totalRevenue, 2),
            'cancelledRevenue' => round($cancelledRevenue, 2),
            'avgOrderValue' => round($avgOrderValue, 2),
            'thisMonthRevenue' => round($thisMonthRevenue, 2),
            'monthlyGrowth' => round($monthlyGrowth, 2),
            'bestSellingProducts' => $bestSellingProducts,
            'leastSellingProducts' => $leastSellingProducts
        ];
    }

    public function products()
    {
        $products = Product::where('user_id', auth()->id())
            ->with(['images', 'category'])
            ->latest()
            ->get();

        // Tüm kategorileri getir
        $categories = Category::all();

        // Kategori ağacını oluştur
        $categoryTree = [];
        foreach ($categories as $category) {
            if (!$category->parent_id) {
                $categoryTree[] = [
                    'category' => $category,
                    'level' => 0
                ];
                $this->buildCategoryTree($categoryTree, $categories, $category->id, 1);
            }
        }

        return view('seller.products', compact('products', 'categories', 'categoryTree'));
    }

    // Kategori ağacını oluşturan yardımcı metod
    private function buildCategoryTree(&$categoryTree, $categories, $parentId, $level)
    {
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $categoryTree[] = [
                    'category' => $category,
                    'level' => $level
                ];
                $this->buildCategoryTree($categoryTree, $categories, $category->id, $level + 1);
            }
        }
    }

    // public function orders()
    // {
    //     // Satıcıya ait siparişler
    //     $orders = Order::where('id', auth()->id())->get(); // seller_id yerine id
    //     return view('seller.orders', compact('orders'));
    // }



    // SellerController.php
    /**
     * Satıcının siparişlerini listeler
     */
    public function orders(Request $request)
    {
        $user = auth()->user();

        // Satıcının kendi ürünlerinin bulunduğu siparişleri al
        $query = Order::whereHas('items.product', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['items.product.images', 'items.product.user']);

        // Status filtresi
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('seller.orders', compact('orders'));
    }

    /**
     * Satıcının siparişlerindeki ürünleri PDF olarak yazdırır
     */
    public function printOrderItems($orderId)
    {
        try {
            $user = auth()->user();

            // Siparişi ve satıcıya ait ürünleri getir
            $order = Order::with(['items.product.images', 'items.product.user'])
                ->whereHas('items.product', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->findOrFail($orderId);

            // Sadece bu satıcıya ait ürünleri filtrele
            $sellerItems = $order->items->filter(function ($item) use ($user) {
                return $item->product && $item->product->user_id == $user->id;
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
            $pdf = PDF::loadView('seller.invoice', compact('order', 'user', 'sellerItems', 'cancelledTotal', 'currentTotal', 'sellerTotal', 'orderNotes', 'trackingInfo'));

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
            $header = view('seller.invoice-header')->render();
            $footer = view('seller.invoice-footer', compact('order', 'trackingInfo'))->render();

            // Header ve footer'ı PDF'e ekle
            $pdf->setOption('header-html', $header);
            $pdf->setOption('footer-html', $footer);
            $pdf->setOption('margin-top', 30);
            $pdf->setOption('margin-bottom', 25);
            $pdf->setOption('margin-left', 15);
            $pdf->setOption('margin-right', 15);
            $pdf->setOption('encoding', 'UTF-8');

            // PDF'i görüntüle
            return $pdf->stream("Fatura-{$order->order_number}-{$user->name}.pdf");

        } catch (\Exception $e) {
            \Log::error('Satıcı faturası oluşturma hatası:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Fatura oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    // Yeni metod - Status güncelleme (sadece seller'ların yapabileceği işlemler)
    public function updateOrderStatus(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            // Bu siparişte satıcının ürünü var mı kontrol et
            $hasSellerProduct = $order->items()
                ->whereHas('product', function ($q) {
                    $q->where('user_id', auth()->id());
                })->exists();

            if (!$hasSellerProduct) {
                return redirect()->back()->with('error', 'Bu siparişte ürününüz bulunmuyor.');
            }

            $request->validate([
                'status' => 'required|in:processing,shipped,delivered',
                'seller_note' => 'nullable|string|max:500'
            ]);

            // Sadece ileri yönde güncelleme yapabilir
            $allowedTransitions = [
                'paid' => ['processing'],
                'processing' => ['shipped'],
                'shipped' => ['delivered']
            ];

            if (!in_array($request->status, $allowedTransitions[$order->status] ?? [])) {
                return redirect()->back()->with('error', 'Geçersiz durum geçişi.');
            }

            $order->update([
                'status' => $request->status,
                'seller_note' => $request->seller_note
            ]);

            return redirect()->back()->with('success', 'Sipariş durumu başarıyla güncellendi.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hata: ' . $e->getMessage());
        }
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
    public function uploadAvatar(Request $request)
    {
        try {
            Log::info('Avatar upload request received', ['files' => $request->allFiles()]);

            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = auth()->user();

            // Eski avatarı sil
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Yeni avatarı yükle
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('avatars', $filename, 'public');

                $user->avatar = $path;
                $user->save();

                Log::info('Avatar uploaded successfully', ['path' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'avatar_url' => asset('storage/' . $user->avatar)
            ]);

        } catch (\Exception $e) {
            Log::error('Avatar upload error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading avatar: ' . $e->getMessage()
            ], 500);
        }
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

        // Kategori ağacını oluştur
        $categoryTree = [];
        foreach ($categories as $category) {
            if (!$category->parent_id) {
                $categoryTree[] = [
                    'category' => $category,
                    'level' => 0
                ];
                $this->buildCategoryTree($categoryTree, $categories, $category->id, 1);
            }
        }

        return view('seller.subcategory-requests', compact('categories', 'subcategoryRequests', 'categoryTree'));
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

    /**
     * Satıcının kendi ürününü iptal etmesi için metod
     */
    public function cancelOrderItem(Request $request, $orderId, $itemId)
    {
        try {
            $request->validate([
                'cancel_reason' => 'required|string|max:500',
                'return_to_stock' => 'nullable|boolean'
            ]);

            $order = Order::findOrFail($orderId);
            $orderItem = $order->items()->findOrFail($itemId);

            // Bu ürün satıcıya ait mi kontrol et
            if ($orderItem->product && $orderItem->product->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Bu ürün size ait değil.');
            }

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
                    'cancellation_reason' => 'Tüm ürünler satıcılar tarafından iptal edildi',
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
            \Log::info('Seller: Order totals after cancellation', $totals);

            return redirect()->back()->with('success', 'Ürün başarıyla iptal edildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ürün iptal edilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sepette unutulan ürünler için hatırlatma e-postası gönderir
     */
    public function sendAbandonedCartEmails(Request $request)
    {
        try {
            $sellerId = auth()->id();

            // Test için süre sınırını kaldırdık - tüm sepet öğelerini al
            $abandonedCarts = Cart::whereHas('product', function ($q) use ($sellerId) {
                $q->where('user_id', $sellerId);
            })
                ->with(['user', 'product.images'])
                ->get()
                ->groupBy('user_id'); // Kullanıcıya göre grupla

            $totalEmailsSent = 0;
            $emailsSentTo = []; // Hangi müşterilere e-posta gönderildiğini takip etmek için

            foreach ($abandonedCarts as $userId => $cartItems) {
                $user = User::find($userId);

                // Kullanıcı yoksa veya e-posta adresi yoksa atla
                if (!$user || !$user->email) {
                    continue;
                }

                // Satıcının ürünlerini hazırla
                $products = [];
                $totalValue = 0;
                $productNames = [];

                foreach ($cartItems as $item) {
                    if ($item->product) {
                        $imagePath = null;
                        $imageUrl = null;
                        $imageBase64 = null;

                        if ($item->product->images && $item->product->images->isNotEmpty()) {
                            $imagePath = $item->product->images->first()->image_path;

                            // Görsel dosyasının tam yolunu al
                            $fullImagePath = storage_path('app/public/' . $imagePath);

                            // Dosya varsa base64 kodla
                            if (file_exists($fullImagePath)) {
                                $imageContent = file_get_contents($fullImagePath);
                                $imageType = pathinfo($fullImagePath, PATHINFO_EXTENSION);
                                $imageBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageContent);
                            }
                        }

                        $products[] = [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                            'quantity' => $item->quantity,
                            'image' => $imagePath,
                            'image_url' => $imageUrl,
                            'image_base64' => $imageBase64,
                            'subtotal' => $item->product->price * $item->quantity
                        ];

                        $totalValue += $item->product->price * $item->quantity;
                        $productNames[] = $item->product->name;
                    }
                }

                // Ürün yoksa e-posta gönderme
                if (empty($products)) {
                    continue;
                }

                // E-posta verilerini hazırla
                $emailData = [
                    'user' => $user,
                    'seller' => auth()->user(),
                    'products' => $products,
                    'totalValue' => $totalValue,
                    'cartUrl' => url('/cart')
                ];

                // E-posta gönder
                Mail::to($user->email)->send(new AbandonedCartReminder($emailData));
                $totalEmailsSent++;

                // Gönderilen e-postaları kaydet
                $emailsSentTo[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'product_count' => count($products),
                    'total_value' => $totalValue,
                    'products' => implode(', ', $productNames)
                ];
            }

            // Konsola gönderilen e-postaları yazdır
            \Log::info('Sepette Unutulan Ürün Hatırlatması E-postaları Gönderildi', [
                'total_sent' => $totalEmailsSent,
                'emails_sent_to' => $emailsSentTo
            ]);

            // AJAX isteği ise JSON yanıtı döndür
            if ($request->ajax()) {
                if ($totalEmailsSent > 0) {
                    return response()->json([
                        'success' => "Toplam {$totalEmailsSent} müşteriye sepette unutulan ürün hatırlatma e-postası gönderildi.",
                        'emails' => $emailsSentTo
                    ]);
                } else {
                    return response()->json([
                        'info' => "Sepette unutulmuş ürün bulunamadı veya hatırlatma e-postası gönderilebilecek müşteri yok."
                    ]);
                }
            }

            // Normal form gönderimi ise redirect ile yanıt ver
            if ($totalEmailsSent > 0) {
                $emailList = collect($emailsSentTo)->pluck('email')->implode(', ');
                return redirect()->back()->with('success', "Toplam {$totalEmailsSent} müşteriye sepette unutulan ürün hatırlatma e-postası gönderildi. Gönderilen e-postalar: {$emailList}");
            } else {
                return redirect()->back()->with('info', "Sepette unutulmuş ürün bulunamadı veya hatırlatma e-postası gönderilebilecek müşteri yok.");
            }

        } catch (\Exception $e) {
            \Log::error('Sepette Unutulan Ürün Hatırlatması E-posta Hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // AJAX isteği ise JSON yanıtı döndür
            if ($request->ajax()) {
                return response()->json([
                    'error' => "E-posta gönderimi sırasında bir hata oluştu: " . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', "E-posta gönderimi sırasında bir hata oluştu: " . $e->getMessage());
        }
    }

    /**
     * Satıcının ürünlerinin sepete eklenme bilgilerini gösterir
     */
    public function cartItems()
    {
        // En son sepete eklenen TÜM ürünler (paginate)
        $latestCartItems = Cart::whereHas('product', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->with(['product.images', 'user'])
            ->orderByDesc('created_at')
            ->paginate(25);

        // En çok sepete eklenen ürünler
        $topCartProducts = Cart::select('product_id', DB::raw('COUNT(*) as cart_count'))
            ->whereHas('product', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->groupBy('product_id')
            ->orderByDesc('cart_count')
            ->with(['product.images'])
            ->take(5)
            ->get();

        // Ürün bazında sepete ekleyen kullanıcılar
        $productUsers = [];
        foreach ($topCartProducts as $item) {
            $productUsers[$item->product_id] = Cart::where('product_id', $item->product_id)
                ->with('user')
                ->take(5)
                ->get()
                ->pluck('user');
        }

        return view('seller.cart-items', compact('latestCartItems', 'topCartProducts', 'productUsers'));
    }

    /**
     * Satıcının ürünlerinin favorilere eklenme bilgilerini gösterir
     */
    public function wishlistItems()
    {
        // En son favorilere eklenen ürünler
        $latestWishlistItems = Wishlist::whereHas('product', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->with(['product.images', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // En çok favorilere eklenen ürünler
        $topWishlistProducts = Wishlist::select('product_id', DB::raw('COUNT(*) as wishlist_count'))
            ->whereHas('product', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->groupBy('product_id')
            ->orderByDesc('wishlist_count')
            ->with(['product.images'])
            ->take(5)
            ->get();

        // Ürün bazında favorilere ekleyen kullanıcılar
        $productUsers = [];

        foreach ($topWishlistProducts as $item) {
            $productUsers[$item->product_id] = Wishlist::where('product_id', $item->product_id)
                ->with('user')
                ->take(5)
                ->get()
                ->pluck('user');
        }

        return view('seller.wishlist-items', compact('latestWishlistItems', 'topWishlistProducts', 'productUsers'));
    }

}
