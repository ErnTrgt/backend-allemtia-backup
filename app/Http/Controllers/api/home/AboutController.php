<?php

namespace App\Http\Controllers\api\home;
use App\Models\AboutSection;
use App\Http\Controllers\Controller;
class AboutController extends Controller
{
    public function index()
    {
        $sections = AboutSection::where('status', 1)->get();

        return response()->json([
            'success' => true,
            'data' => $sections
        ]);
    }
}
