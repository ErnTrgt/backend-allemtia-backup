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
                    $query->select('id', 'name', 'parent_id'); // Alt kategoriler
                }
            ])
            ->get(['id', 'name', 'parent_id']);

        return response()->json([
            'success' => true,
            'message' => 'Kategoriler getirildi',
            'data' => $categories
        ]);
    }
}
