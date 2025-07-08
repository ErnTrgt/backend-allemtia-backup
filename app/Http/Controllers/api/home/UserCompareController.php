<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Compare;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCompareController extends Controller
{
    /**
     * Kullanıcının karşılaştırma listesindeki ürünleri getir
     */
    public function index()
    {
        $user = Auth::user();
        $compareItems = Compare::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $compareItems
        ]);
    }

    /**
     * Karşılaştırma listesine ürün ekle
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_data' => 'required|json',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Ürün karşılaştırma listesinde var mı kontrol et
        $compareItem = Compare::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($compareItem) {
            return response()->json([
                'success' => true,
                'data' => $compareItem,
                'message' => 'Ürün zaten karşılaştırma listenizde.'
            ]);
        } else {
            // Karşılaştırma listesine yeni ekle
            $compareItem = Compare::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_data' => $request->product_data,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $compareItem,
            'message' => 'Ürün karşılaştırma listesine eklendi.'
        ]);
    }

    /**
     * Karşılaştırma listesinden ürün sil
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $compareItem = Compare::where('user_id', $user->id)->findOrFail($id);

        $compareItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ürün karşılaştırma listesinden silindi.'
        ]);
    }

    /**
     * Karşılaştırma listesini temizle
     */
    public function clear()
    {
        $user = Auth::user();
        Compare::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karşılaştırma listesi temizlendi.'
        ]);
    }

    /**
     * Karşılaştırma listesindeki ürünlerin sayısını getir
     */
    public function count()
    {
        $user = Auth::user();
        $count = Compare::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data' => $count
        ]);
    }

    /**
     * Bulk karşılaştırma listesi güncellemesi
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

        // Önce mevcut karşılaştırma listesini temizle
        Compare::where('user_id', $user->id)->delete();

        // Yeni ürünleri ekle
        foreach ($items as $item) {
            Compare::create([
                'user_id' => $user->id,
                'product_id' => $item['product_id'],
                'product_data' => $item['product_data'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Karşılaştırma listesi güncellendi.'
        ]);
    }
}