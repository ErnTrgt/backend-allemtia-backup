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

    public function showBlog($id)
    {
        $blog = Blog::where('status', 1)->find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'content' => $blog->content,
                'blog_img' => $blog->blog_img,
                'date' => $blog->date->format('d.m.Y'),
                'author' => $blog->author,
                'description' => $blog->description,
                'tags' => $blog->tags
            ]
        ]);
    }


}