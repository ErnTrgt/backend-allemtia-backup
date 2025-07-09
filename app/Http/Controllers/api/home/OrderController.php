<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Basitleştirilmiş debug
        Log::info('=== ORDER STORE DEBUG ===');
        Log::info('1. Auth check:', ['result' => Auth::check()]);
        Log::info('2. Auth user:', ['user' => Auth::user()]);
        Log::info('3. Auth ID:', ['id' => Auth::id()]);
        Log::info('4. Request user:', ['user' => $request->user()]);
        Log::info('5. Frontend user_id:', ['user_id' => $request->user_id]);

        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'shipping_address' => 'required|array',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postCode' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'payment_method' => 'required|in:eft,cash',
            'payment_reference' => 'nullable|string',
            'selected_bank' => 'nullable|string',
            'total' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // User ID'yi al - önce auth'dan, yoksa request'ten
            $userId = Auth::id() ?: $request->user_id;

            Log::info('Final User ID:', ['user_id' => $userId]);

            // Order numarası için kontrol ekle
            // Eğer EFT ödemesi ve referans kodu varsa onu kullan, yoksa standart ORD-timestamp kullan
            $orderNumber = 'ORD-' . time();
            if ($validated['payment_method'] === 'eft' && !empty($validated['payment_reference'])) {
                $orderNumber = $validated['payment_reference'];
                Log::info('Using payment reference as order number:', ['reference' => $orderNumber]);
            }

            // Order oluştur
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => json_encode($validated['shipping_address']),
                'total' => $validated['total'],
                'status' => $validated['payment_method'] === 'cash' ? 'pending' : 'waiting_payment',
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'selected_bank' => $validated['selected_bank'] ?? null,
                'notes' => $validated['notes'] ?? null
            ]);

            // Order items oluştur ve stok kontrolü
            foreach ($validated['items'] as $item) {
                // Ürün stok kontrolü
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Ürün bulunamadı: " . $item['product_name']);
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok yetersiz: " . $item['product_name'] . " (Mevcut: " . $product->stock . ", İstenen: " . $item['quantity'] . ")");
                }

                // OrderItem oluştur
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity']
                ]);

                // Stok güncelle
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Siparişiniz başarıyla oluşturuldu',
                'order' => $order->load('items'),
                'order_number' => $order->order_number,
                'user_id' => $userId // Debug için
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        // Sipariş sahibi veya admin kontrolü
        if (Auth::id() !== $order->user_id && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Bu siparişi görüntüleme yetkiniz yok'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'order' => $order->load('items')
        ]);
    }

    public function userOrders(Request $request)
    {
        try {
            // Debug için log ekleyin
            \Log::info('UserOrders called', [
                'user' => $request->user(),
                'user_id' => $request->user() ? $request->user()->id : null
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $orders = Order::where('user_id', $user->id)
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Orders found:', ['count' => $orders->count()]);

            return response()->json([
                'success' => true,
                'orders' => $orders,
                'count' => $orders->count() // Debug için
            ]);

        } catch (\Exception $e) {
            \Log::error('UserOrders error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Siparişler yüklenirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Sipariş içindeki tek bir ürün için iptal
    public function cancelOrderItem(Request $request, Order $order, $itemId)
    {
        try {
            // Debug için log ekle
            \Log::info('Cancel Order Item Request', [
                'order_id' => $order->id,
                'item_id' => $itemId,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Sipariş sahibi kontrolü
            if (Auth::id() !== $order->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu siparişi iptal etme yetkiniz yok'
                ], 403);
            }

            // Sipariş durumu kontrolü
            if (in_array($order->status, ['delivered', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu siparişteki ürünler artık iptal edilemez. Siparişiniz teslim edilmiş veya tamamen iptal edilmiş durumda.'
                ], 400);
            }

            DB::beginTransaction();

            // Ürünü bul
            $item = OrderItem::where('order_id', $order->id)
                ->where('id', $itemId)
                ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belirtilen ürün bu siparişte bulunamadı'
                ], 404);
            }

            if ($item->is_cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün zaten iptal edilmiş'
                ], 400);
            }

            // İptal nedeni
            $reason = $request->input('cancel_reason') ?: $request->input('reason');
            if (!$reason) {
                $reason = "Müşteri tarafından iptal edildi";
            }

            // Ürün iptal et
            $result = $order->cancelItem($itemId, $reason);

            if (!$result) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün iptal edilemedi'
                ], 400);
            }

            // Tüm ürünler iptal edilmiş mi kontrol et
            $allCancelled = $order->items()->where('is_cancelled', false)->count() === 0;

            if ($allCancelled) {
                $order->status = 'cancelled';
                $order->cancellation_reason = 'Tüm ürünler iptal edildi';
                $order->cancelled_at = now();
                $order->is_partially_cancelled = false; // Tamamen iptal edildiği için kısmi iptal bayrağını kaldır
            } else {
                // Status'u değiştirmeden sadece flag'i set et
                $order->is_partially_cancelled = true;
            }

            // Toplam tutarı güncelle
            $totals = $order->updateTotalAmount();
            \Log::info('Order totals after cancellation', $totals);

            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla iptal edildi',
                'order' => $order->fresh()->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Order item cancellation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'item_id' => $itemId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ürün iptal edilirken bir hata oluştu: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
}