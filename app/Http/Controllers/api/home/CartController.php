<?php
// app/Http/Controllers/api/home/CartController.php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CartController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|integer',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.price' => 'required|numeric|min:0',
        ]);

        // Kuponu bul
        $coupon = Coupon::where('code', $data['code'])
            ->where('active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon bulunamadı veya geçersiz.',
            ], 404);
        }

        // Tarih kontrolü - başlangıç tarihi
        if ($coupon->starts_at && Carbon::parse($coupon->starts_at)->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kupon henüz aktif değil.',
            ], 400);
        }

        // Tarih kontrolü - bitiş tarihi
        if ($coupon->expires_at && Carbon::parse($coupon->expires_at)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kuponun süresi dolmuş.',
            ], 400);
        }

        // Kullanım limiti kontrolü
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kuponun kullanım limiti dolmuş.',
            ], 400);
        }

        // *** DÜZELTME: Kullanıcı başına bir kez kontrolü ***
        if ($coupon->once_per_user) {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            $ipAddress = $request->ip();

            // Kullanıcı için benzersiz anahtar oluştur
            $userKey = $userId ? "user_$userId" : "session_{$sessionId}_ip_{$ipAddress}";
            $cacheKey = "coupon_used_{$coupon->id}_{$userKey}";

            // Bu kullanıcının bu kuponu daha önce kullanıp kullanmadığını kontrol et
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kuponu daha önce kullandınız. Her kupon sadece bir kez kullanılabilir.',
                ], 400);
            }
        }

        $subtotal = $data['subtotal'];
        $cartItems = $data['cart_items'];

        // İndirim hesaplaması için temel tutar
        $baseForDiscount = 0;
        $applicableProducts = [];

        if ($coupon->seller_id) {
            // Satıcı kuponu: Belirli satıcının ürünlerine uygulanır
            $pivotProductIds = $coupon->products()->pluck('products.id')->toArray();

            if (empty($pivotProductIds)) {
                // Eğer pivot boşsa, o seller'ın tüm ürünleri geçerli
                $pivotProductIds = Product::where('user_id', $coupon->seller_id)
                    ->pluck('id')
                    ->toArray();
            }

            // Sepetteki ürünleri kontrol et
            foreach ($cartItems as $item) {
                $productId = $item['product_id'];
                $price = $item['price'];
                $quantity = $item['quantity'];

                if (in_array($productId, $pivotProductIds)) {
                    $itemTotal = $price * $quantity;
                    $baseForDiscount += $itemTotal;
                    $applicableProducts[] = [
                        'product_id' => $productId,
                        'price' => $price,
                        'quantity' => $quantity,
                        'total' => $itemTotal
                    ];
                }
            }

            if ($baseForDiscount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kupon sepetteki hiçbir ürüne uygulanamaz.',
                ], 400);
            }

            // Minimum sipariş tutarı kontrolü
            if ($coupon->min_order_amount && $baseForDiscount < $coupon->min_order_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu kupon en az ₺{$coupon->min_order_amount} tutarındaki ilgili ürünlerde geçerli.",
                ], 400);
            }

        } else {
            // Admin kuponu: Tüm sepete uygulanır
            $pivotProductIds = $coupon->products()->pluck('products.id')->toArray();

            if (!empty($pivotProductIds)) {
                // Belirli ürünlere özel kupon
                foreach ($cartItems as $item) {
                    $productId = $item['product_id'];
                    $price = $item['price'];
                    $quantity = $item['quantity'];

                    if (in_array($productId, $pivotProductIds)) {
                        $itemTotal = $price * $quantity;
                        $baseForDiscount += $itemTotal;
                        $applicableProducts[] = [
                            'product_id' => $productId,
                            'price' => $price,
                            'quantity' => $quantity,
                            'total' => $itemTotal
                        ];
                    }
                }

                if ($baseForDiscount <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu kupon sepetteki hiçbir ürüne uygulanamaz.',
                    ], 400);
                }
            } else {
                // Genel kupon - tüm sepete uygulanır
                $baseForDiscount = $subtotal;
                foreach ($cartItems as $item) {
                    $applicableProducts[] = [
                        'product_id' => $item['product_id'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity']
                    ];
                }
            }

            // Minimum sipariş tutarı kontrolü
            if ($coupon->min_order_amount && $baseForDiscount < $coupon->min_order_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu kupon en az ₺{$coupon->min_order_amount} tutarlı sepetlerde geçerli.",
                ], 400);
            }
        }

        // İndirim hesaplama
        $discount = $this->calculateDiscount($coupon, $baseForDiscount);

        // İndirim, uygulanan ürünlerin toplamını aşamasın
        $discount = min($discount, $baseForDiscount);

        // Yeni toplam hesapla
        $newTotal = max(0, $subtotal - $discount);

        // *** DÜZELTME: Sadece cache'e kaydet, used_count'u artırma ***
        if ($coupon->once_per_user) {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            $ipAddress = $request->ip();

            // Kullanıcı için benzersiz anahtar oluştur
            $userKey = $userId ? "user_$userId" : "session_{$sessionId}_ip_{$ipAddress}";
            $cacheKey = "coupon_applied_{$coupon->id}_{$userKey}"; // applied olarak işaretle

            // Kuponun uygulandığını geçici olarak işaretle (1 saat boyunca)
            $cacheData = [
                'coupon_code' => $coupon->code,
                'applied_at' => now()->toDateTimeString(),
                'discount_amount' => $discount,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'subtotal' => $subtotal,
                'cart_hash' => md5(json_encode($cartItems)) // Sepet değişikliği kontrolü
            ];

            Cache::put($cacheKey, $cacheData, now()->addHour()); // 1 saat geçici
        }

        // *** ÖNEMLI: used_count'u burada artırma, sipariş tamamlandığında artır ***

        // Her ürün için indirim hesapla
        $productDiscounts = [];
        $totalDiscountCheck = 0;

        foreach ($applicableProducts as $product) {
            $productTotal = $product['total'];
            $productDiscountRatio = $productTotal / $baseForDiscount;
            $productDiscount = $discount * $productDiscountRatio;

            $productDiscounts[] = [
                'product_id' => $product['product_id'],
                'original_price' => $product['price'],
                'quantity' => $product['quantity'],
                'original_total' => $productTotal,
                'discount_amount' => round($productDiscount, 2),
                'discounted_price' => round($product['price'] - ($productDiscount / $product['quantity']), 2),
                'discounted_total' => round($productTotal - $productDiscount, 2),
                'discount_percentage' => $coupon->type === 'percent' ? $coupon->value : round(($productDiscount / $product['quantity'] / $product['price']) * 100, 1)
            ];

            $totalDiscountCheck += $productDiscount;
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'seller_id' => $coupon->seller_id,
                'description' => $this->getCouponDescription($coupon),
            ],
            'discount' => round($discount, 2),
            'newTotal' => round($newTotal, 2),
            'subtotal' => round($subtotal, 2),
            'baseForDiscount' => round($baseForDiscount, 2),
            'applicableProducts' => $applicableProducts,
            'productDiscounts' => $productDiscounts,
            'discountSummary' => [
                'total_items_count' => count($applicableProducts),
                'total_discount' => round($discount, 2),
                'average_discount_percentage' => $coupon->type === 'percent' ? $coupon->value : round(($discount / $baseForDiscount) * 100, 1)
            ],
            'message' => 'Kupon başarıyla uygulandı.',
        ]);
    }

    private function getCouponDescription($coupon)
    {
        switch ($coupon->type) {
            case 'percent':
                return "%{$coupon->value} indirim";
            case 'fixed':
                return "₺{$coupon->value} indirim";
            case 'free_shipping':
                return "Ücretsiz kargo";
            default:
                return "İndirim";
        }
    }

    // Sepet verilerini kupon bilgisi ile birleştir
    public function getCartWithCouponInfo(Request $request)
    {
        $data = $request->validate([
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|integer',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.price' => 'required|numeric|min:0',
        ]);

        $cartItems = $data['cart_items'];
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();
        $userKey = $userId ? "user_$userId" : "session_{$sessionId}_ip_{$ipAddress}";

        // Aktif kupon kontrolü
        $activeCoupon = null;
        $productDiscounts = [];

        // Cache'den aktif kupon ara
        $allCoupons = Coupon::where('active', true)->get();
        foreach ($allCoupons as $coupon) {
            $appliedCacheKey = "coupon_applied_{$coupon->id}_{$userKey}";
            if (Cache::has($appliedCacheKey)) {
                $activeCoupon = $coupon;
                break;
            }
        }

        // Ürünleri zenginleştir
        $enrichedCartItems = [];
        foreach ($cartItems as $item) {
            $productId = $item['product_id'];
            $isDiscounted = false;
            $discountInfo = null;

            // Bu ürün indirimli mi kontrol et
            if ($activeCoupon) {
                $appliedCacheKey = "coupon_applied_{$activeCoupon->id}_{$userKey}";
                $appliedData = Cache::get($appliedCacheKey);

                // Sepet hash'ini kontrol et
                $currentCartHash = md5(json_encode($cartItems));
                if ($appliedData && $appliedData['cart_hash'] === $currentCartHash) {
                    // Bu ürün indirimli ürünler arasında mı?
                    // Kupon mantığını tekrar çalıştır (basitleştirilmiş)
                    $isProductApplicable = $this->isProductApplicableForCoupon($activeCoupon, $productId);

                    if ($isProductApplicable) {
                        $isDiscounted = true;
                        $discountInfo = [
                            'coupon_code' => $activeCoupon->code,
                            'discount_type' => $activeCoupon->type,
                            'discount_value' => $activeCoupon->value,
                        ];
                    }
                }
            }

            $enrichedCartItems[] = [
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
                'is_discounted' => $isDiscounted,
                'discount_info' => $discountInfo
            ];
        }

        return response()->json([
            'success' => true,
            'cart_items' => $enrichedCartItems,
            'active_coupon' => $activeCoupon ? [
                'code' => $activeCoupon->code,
                'type' => $activeCoupon->type,
                'value' => $activeCoupon->value,
                'description' => $this->getCouponDescription($activeCoupon)
            ] : null
        ]);
    }

    private function isProductApplicableForCoupon($coupon, $productId)
    {
        if ($coupon->seller_id) {
            // Satıcı kuponu
            $pivotProductIds = $coupon->products()->pluck('products.id')->toArray();

            if (empty($pivotProductIds)) {
                // Seller'ın tüm ürünleri
                return Product::where('user_id', $coupon->seller_id)
                    ->where('id', $productId)
                    ->exists();
            } else {
                // Belirli ürünler
                return in_array($productId, $pivotProductIds);
            }
        } else {
            // Admin kuponu
            $pivotProductIds = $coupon->products()->pluck('products.id')->toArray();

            if (empty($pivotProductIds)) {
                // Tüm ürünler
                return true;
            } else {
                // Belirli ürünler
                return in_array($productId, $pivotProductIds);
            }
        }
    }

    private function calculateDiscount($coupon, $baseAmount)
    {
        switch ($coupon->type) {
            case 'percent':
                return ($baseAmount * $coupon->value) / 100;
            case 'fixed':
                return $coupon->value;
            case 'free_shipping':
                return $coupon->value ?? 0;
            default:
                return 0;
        }
    }

    public function removeCoupon(Request $request)
    {
        // Kupon kaldırıldığında cache'i de temizle
        $data = $request->validate([
            'coupon_code' => 'nullable|string',
        ]);

        if (isset($data['coupon_code'])) {
            $coupon = Coupon::where('code', $data['coupon_code'])->first();
            if ($coupon && $coupon->once_per_user) {
                $userId = Auth::id();
                $sessionId = $request->session()->getId();
                $ipAddress = $request->ip();
                $userKey = $userId ? "user_$userId" : "session_{$sessionId}_ip_{$ipAddress}";
                $cacheKey = "coupon_applied_{$coupon->id}_{$userKey}";

                Cache::forget($cacheKey);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kupon kaldırıldı.',
        ]);
    }

    // *** YENİ: Sipariş tamamlandığında kupon kullanımını kesinleştir ***
    public function confirmCouponUsage(Request $request)
    {
        $data = $request->validate([
            'coupon_code' => 'required|string',
            'order_id' => 'required|integer',
        ]);

        $coupon = Coupon::where('code', $data['coupon_code'])->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon bulunamadı.',
            ], 404);
        }

        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();
        $userKey = $userId ? "user_$userId" : "session_{$sessionId}_ip_{$ipAddress}";

        // Geçici cache'den veriyi al
        $appliedCacheKey = "coupon_applied_{$coupon->id}_{$userKey}";
        $appliedData = Cache::get($appliedCacheKey);

        if (!$appliedData) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon uygulama kaydı bulunamadı.',
            ], 400);
        }

        // Kullanım sayısını artır
        $coupon->increment('used_count');

        // Kalıcı kullanım kaydı oluştur
        if ($coupon->once_per_user) {
            $usedCacheKey = "coupon_used_{$coupon->id}_{$userKey}";
            $usedData = [
                'coupon_code' => $coupon->code,
                'used_at' => now()->toDateTimeString(),
                'discount_amount' => $appliedData['discount_amount'],
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'order_id' => $data['order_id']
            ];

            Cache::put($usedCacheKey, $usedData, now()->addDays(30));
        }

        // Geçici cache'i temizle
        Cache::forget($appliedCacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Kupon kullanımı kesinleştirildi.',
        ]);
    }

    // Kullanıcının kullandığı kuponları göster
    public function getUserUsedCoupons(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();

        $userKey = $userId ? "user_$userId" : "session_{$sessionId}_ip_{$ipAddress}";

        // Tüm kuponları al ve bu kullanıcı tarafından kullanılanları bul
        $allCoupons = Coupon::where('once_per_user', true)->get();
        $usedCoupons = [];

        foreach ($allCoupons as $coupon) {
            $cacheKey = "coupon_used_{$coupon->id}_{$userKey}";
            if (Cache::has($cacheKey)) {
                $cacheData = Cache::get($cacheKey);
                $usedCoupons[] = [
                    'coupon_id' => $coupon->id,
                    'coupon_code' => $coupon->code,
                    'used_at' => $cacheData['used_at'],
                    'discount_amount' => $cacheData['discount_amount'],
                    'order_id' => $cacheData['order_id'] ?? null
                ];
            }
        }

        return response()->json([
            'success' => true,
            'used_coupons' => $usedCoupons
        ]);
    }

    // Admin için kupon kullanım temizleme
    public function clearCouponUsage(Request $request)
    {
        $data = $request->validate([
            'coupon_id' => 'required|integer',
            'user_id' => 'nullable|integer',
            'session_id' => 'nullable|string',
        ]);

        $couponId = $data['coupon_id'];
        $userId = $data['user_id'] ?? null;
        $sessionId = $data['session_id'] ?? null;

        if ($userId) {
            $userKey = "user_$userId";
        } elseif ($sessionId) {
            $userKey = "session_$sessionId";
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı ID veya Session ID gerekli.'
            ], 400);
        }

        // Hem geçici hem kalıcı cache'i temizle
        $appliedCacheKey = "coupon_applied_{$couponId}_{$userKey}";
        $usedCacheKey = "coupon_used_{$couponId}_{$userKey}";

        Cache::forget($appliedCacheKey);
        Cache::forget($usedCacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Kupon kullanım kaydı temizlendi.'
        ]);
    }
}