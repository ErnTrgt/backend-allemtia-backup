<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\api\BaseController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->select('id', 'name', 'parent_id')
                        ->with([
                            'children' => function ($query) {
                                $query->select('id', 'name', 'parent_id')
                                    ->with([
                                        'children' => function ($query) {
                                            $query->select('id', 'name', 'parent_id');
                                        }
                                    ]);
                            }
                        ]);
                }
            ])
            ->get(['id', 'name', 'parent_id']);

        return response()->json([
            'success' => true,
            'message' => 'Kategoriler getirildi',
            'data' => $categories
        ]);
    }

    public function getAllCategories()
    {
        // Tüm kategorileri düz bir liste olarak getir
        $categories = Category::select('id', 'name', 'parent_id')->get();

        // Kategori ağacını oluştur
        $categoryTree = [];
        foreach ($categories as $category) {
            if (!$category->parent_id) {
                $category->level = 0;
                $category->prefix = '';
                $categoryTree[] = $category;
                $this->buildCategoryTree($categoryTree, $categories, $category->id, 1);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tüm kategoriler getirildi',
            'data' => $categoryTree
        ]);
    }

    private function buildCategoryTree(&$categoryTree, $categories, $parentId, $level)
    {
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $category->level = $level;
                $category->prefix = str_repeat('-- ', $level);
                $categoryTree[] = $category;
                $this->buildCategoryTree($categoryTree, $categories, $category->id, $level + 1);
            }
        }
    }

    public function getProductsByCategory($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);

            // Kategori ve tüm alt kategorilerin ID'lerini topla
            $categoryIds = [$categoryId];
            $this->getAllChildCategoryIds($category, $categoryIds);

            // Debug için kategori ID'lerini logla
            \Log::info('Kategori ve alt kategorileri: ' . implode(', ', $categoryIds));

            // Bu kategorilere ait ürünleri getir
            $products = \App\Models\Product::whereIn('category_id', $categoryIds)
                ->where(function ($query) {
                    $query->where('status', 'active')
                        ->orWhereNull('status');
                })
                ->with(['images', 'user'])
                ->get();

            // Debug için ürün sayısını logla
            \Log::info('Bulunan ürün sayısı: ' . $products->count());

            return response()->json([
                'success' => true,
                'message' => 'Kategori ürünleri getirildi',
                'data' => $products,
                'debug' => [
                    'category_id' => $categoryId,
                    'category_name' => $category->name,
                    'all_category_ids' => $categoryIds,
                    'product_count' => $products->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Kategori ürünleri getirme hatası: ' . $e->getMessage());

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

    private function getAllChildCategoryIds($category, &$categoryIds)
    {
        foreach ($category->children as $child) {
            $categoryIds[] = $child->id;
            $this->getAllChildCategoryIds($child, $categoryIds);
        }
    }
}
