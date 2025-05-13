<?php
namespace App\Http\Controllers\Api\home;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('status', 1)->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }
}

