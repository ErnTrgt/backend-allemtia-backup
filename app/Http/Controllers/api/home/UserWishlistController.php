<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWishlistController extends Controller
{
    /**
     * Kullanıcının favorilerindeki ürünleri getir
     */
    public function index()
    {
        $user = Auth::user();
        $wishlistItems = Wishlist::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $wishlistItems
        ]);
    }

    /**
     * Favorilere ürün ekle
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_data' => 'required|json',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Ürün favorilerde var mı kontrol et
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlistItem) {
            return response()->json([
                'success' => true,
                'data' => $wishlistItem,
                'message' => 'Ürün zaten favorilerinizde.'
            ]);
        } else {
            // Favorilere yeni ekle
            $wishlistItem = Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_data' => $request->product_data,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $wishlistItem,
            'message' => 'Ürün favorilere eklendi.'
        ]);
    }

    /**
     * Favorilerden ürün sil
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)->findOrFail($id);

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ürün favorilerden silindi.'
        ]);
    }

    /**
     * Favorileri temizle
     */
    public function clear()
    {
        $user = Auth::user();
        Wishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Favoriler temizlendi.'
        ]);
    }

    /**
     * Favorilerdeki ürünlerin sayısını getir
     */
    public function count()
    {
        $user = Auth::user();
        $count = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data' => $count
        ]);
    }

    /**
     * Bulk favoriler güncellemesi
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_data' => 'required|json',
        ]);

        $user = Auth::user();
        $items = $request->items;

        // Önce mevcut favorileri temizle
        Wishlist::where('user_id', $user->id)->delete();

        // Yeni ürünleri ekle
        foreach ($items as $item) {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $item['product_id'],
                'product_data' => $item['product_data'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Favoriler güncellendi.'
        ]);
    }
}