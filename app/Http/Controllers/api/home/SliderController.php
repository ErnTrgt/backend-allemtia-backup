<?php
namespace App\Http\Controllers\Api\home;

use App\Http\Controllers\Controller;
use App\Models\Slider;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('status', 1)->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $sliders
        ]);
    }
}
