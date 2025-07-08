<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCartController extends Controller
{
    /**
     * Kullanıcının sepetindeki ürünleri getir
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems
        ]);
    }

    /**
     * Sepete ürün ekle veya güncelle
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_data' => 'required|json',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Ürün sepette var mı kontrol et
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Sepette varsa güncelle
            $cartItem->quantity = $request->quantity;
            $cartItem->product_data = $request->product_data;
            $cartItem->save();
        } else {
            // Sepette yoksa yeni ekle
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'product_data' => $request->product_data,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $cartItem,
            'message' => 'Ürün sepete eklendi.'
        ]);
    }

    /**
     * Sepette bir ürünü güncelle
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)->findOrFail($id);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'data' => $cartItem,
            'message' => 'Sepet güncellendi.'
        ]);
    }

    /**
     * Sepetten ürün sil
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)->findOrFail($id);

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ürün sepetten silindi.'
        ]);
    }

    /**
     * Sepeti temizle
     */
    public function clear()
    {
        $user = Auth::user();
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sepet temizlendi.'
        ]);
    }

    /**
     * Sepetteki ürünlerin toplam sayısını getir
     */
    public function count()
    {
        $user = Auth::user();
        $count = Cart::where('user_id', $user->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => $count
        ]);
    }

    /**
     * Bulk sepet güncellemesi (çoklu ürünleri bir defada ekle)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.product_data' => 'required|json',
        ]);

        $user = Auth::user();
        $items = $request->items;

        // Önce mevcut sepeti temizle
        Cart::where('user_id', $user->id)->delete();

        // Yeni ürünleri ekle
        foreach ($items as $item) {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'product_data' => $item['product_data'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sepet güncellendi.'
        ]);
    }
}