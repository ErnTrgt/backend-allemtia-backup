<?php
namespace App\Http\Controllers\api\home;

use App\Http\Controllers\api\BaseController;
use App\Models\ProductModel;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    // **Tüm Ürünleri Getir**
    public function index()
    {
        $products = ProductModel::with('images')->get();

        // Ürün görsellerini ekleyelim
        $products->map(function ($product) {
            $product->productImg = $product->images->isNotEmpty()
                ? url('storage/' . $product->images->first()->image_path)
                : url('storage/products/default-product.jpg');
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Tüm Ürünler Getirildi',
            'data' => $products
        ]);
    }

    // **Kategoriye Göre Ürünleri Getir**
    public function getProductsByCategory($categoryId)
    {
        // **Kategori mevcut mu kontrol et**
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
                'data' => []
            ], 404);
        }

        // **Kategoriye ve alt kategorilere ait ürünleri getir**
        $categoryIds = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        $categoryIds[] = $categoryId; // Ana kategoriyi de ekle

        $products = ProductModel::whereIn('category_id', $categoryIds)->with('images')->get();

        // **Ürünlere görselleri ekleyelim**
        $products->map(function ($product) {
            $product->productImg = $product->images->isNotEmpty()
                ? url('storage/' . $product->images->first()->image_path)
                : url('storage/products/default-product.jpg');
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Kategoriye ait ürünler getirildi',
            'data' => $products
        ]);
    }

    //tek bir ürünü idsine göre getirme endpointi
    public function show($id)
    {
        $product = ProductModel::with('images')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
                'data' => null,
            ], 404);
        }

        // Ürün görselini ekleyelim
        $product->productImg = $product->images->isNotEmpty()
            ? url('storage/' . $product->images->first()->image_path)
            : url('storage/products/default-product.jpg');

        return response()->json([
            'success' => true,
            'message' => 'Ürün getirildi',
            'data' => $product,
        ]);
    }




}






