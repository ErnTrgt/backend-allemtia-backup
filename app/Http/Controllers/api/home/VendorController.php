<?php

namespace App\Http\Controllers\Api\home;

use App\Http\Controllers\Controller;
use App\Models\User;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = User::where('role', 'seller')
            ->where('status', 'approved')
            ->get()
            ->map(function ($vendor) {
                return [
                    'vendorId' => $vendor->id,
                    'vendor' => $vendor->name,
                    'email' => $vendor->email,
                    'vendorImg' => $vendor->avatar ?? 'default.jpg',
                    'vendorBanner' => $vendor->banner_image ?? 'default-banner.jpg',
                    'verified' => $vendor->approved == 1,
                    'averageRating' => $vendor->rating ?? 4.2,
                    'Items' => $vendor->products()->count() ?? 0,
                    'Sells' => $vendor->orders()->count() ?? 0,
                    'vendorStatus' => ucfirst($vendor->status),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vendors,
        ]);
    }
    // app/Http/Controllers/Api/VendorController.php
    public function show($id)                // ⬅️ tekil satıcı
    {
        $u = User::where('role', 'seller')
            ->where('status', 'approved')
            ->findOrFail($id);

        $vendor = [
            'vendorId' => $u->id,
            'vendor' => $u->name,
            'email' => $u->email,
            'vendorImg' => $u->profile_photo ?? 'default.jpg',
            'vendorBanner' => $u->banner_image ?? 'default-banner.jpg',
            'verified' => $u->approved == 1,
            'averageRating' => $u->rating ?? 4.2,
            //'totalRating' => $u->reviews()->count() ?? 0,
            'Items' => $u->products()->count() ?? 0,
            'Sells' => $u->orders()->count() ?? 0,
            'vendorStatus' => ucfirst($u->status),
            'description' => $u->bio ?? '',      // varsa
            'phone' => $u->phone ?? '',

        ];

        return response()->json(['success' => true, 'data' => $vendor]);
    }
    public function products($id)
    {
        $user = User::where('role', 'seller')
            ->where('status', 'approved')
            ->with(['products.images'])   // önemli: eager‑load
            ->findOrFail($id);

        $products = $user->products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'image' => optional($p->images->first())->image_path
                    ?? 'default-product.jpg',
            ];
        });

        return response()->json(['success' => true, 'data' => $products]);
    }



}
