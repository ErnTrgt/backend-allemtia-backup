<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\api\BaseController;
use App\Models\Blog; // Blog modelini dahil edin
use Illuminate\Http\Request;

class BlogController extends BaseController
{
    public function index(Request $request)
    {
        $blogs = Blog::orderBy('id', 'asc')->get();

        // BaseController'dan success methodunu kullanarak JSON yanıt dönüyoruz
        return parent::success("Bloglar getirildi", $blogs);
    }
}
