<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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

            // Order oluştur
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-' . time(),
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

            // Order items oluştur
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity']
                ]);
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
                'message' => 'Sipariş oluşturulurken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'order' => $order->load('items')
        ]);
    }
    // app/Http/Controllers/api/home/OrderController.php

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


}