<?php
namespace App\Http\Controllers\api\home;

use App\Http\Controllers\api\BaseController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    // **Tüm Ürünleri Getir**
    public function index()
    {
        $products = Product::with('images')->get();

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
        try {
            // **Kategori mevcut mu kontrol et ve alt kategorilerini yükle**
            $category = Category::with('children.children')->find($categoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı',
                    'data' => []
                ], 404);
            }

            // Kategori ve tüm alt kategorilerin ID'lerini topla
            $categoryIds = [$categoryId];
            $this->getAllChildCategoryIds($category, $categoryIds);

            // Debug için kategori ID'lerini logla
            \Log::info('ProductController: Kategori ve alt kategorileri: ' . implode(', ', $categoryIds));

            $products = Product::whereIn('category_id', $categoryIds)
                ->with('images')
                ->get();

            // Debug için ürün sayısını logla
            \Log::info('ProductController: Bulunan ürün sayısı: ' . $products->count());

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
                'data' => $products,
                'debug' => [
                    'category_id' => $categoryId,
                    'category_name' => $category->name,
                    'all_category_ids' => $categoryIds,
                    'product_count' => $products->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductController: Kategori ürünleri getirme hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Kategori ürünleri getirilirken hata oluştu: ' . $e->getMessage(),
                'data' => [],
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    // Alt kategorilerin ID'lerini toplayan yardımcı metod
    private function getAllChildCategoryIds($category, &$categoryIds)
    {
        // children ilişkisi üzerinden alt kategorileri al
        if (method_exists($category, 'children')) {
            foreach ($category->children as $child) {
                $categoryIds[] = $child->id;
                $this->getAllChildCategoryIds($child, $categoryIds);
            }
        }
    }

    //tek bir ürünü idsine göre getirme endpointi
    public function show($id)
    {
        $product = Product::with('images')->find($id);

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






