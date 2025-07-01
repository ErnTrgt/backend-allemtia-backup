<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Blog listesi (API)
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $blogs = Blog::where('status', true)
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        $data = $blogs->map(function ($blog) {
            return $blog->toApiArray();
        });

        return response()->json([
            'data' => $data,
            'current_page' => $blogs->currentPage(),
            'last_page' => $blogs->lastPage(),
            'per_page' => $blogs->perPage(),
            'total' => $blogs->total()
        ]);
    }

    /**
     * Tek blog detayı (API)
     */
    public function show($id)
    {
        $blog = Blog::where('status', true)->findOrFail($id);

        return response()->json([
            'data' => $blog->toApiArray()
        ]);
    }
}