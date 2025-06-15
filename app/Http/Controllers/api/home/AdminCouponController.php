<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCouponController extends Controller
{
    public function __construct()
    {
        // Burada global olarak policy’leri çalıştıralım:
        $this->authorizeResource(Coupon::class, 'coupon');
    }

    /**
     * Super-Admin için: Tüm kuponları listele
     */
    public function index()
    {
        // Policy içeride zaten viewAny kontrolü yapacak
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $coupons]);
    }

    /**
     * Yeni kupon oluştur
     */
    public function store(Request $request)
    {
        $this->authorize('create', Coupon::class);

        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => ['required', Rule::in(['fixed', 'percent', 'free_shipping'])],
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'active' => 'boolean',
            // superadmin oluşturduğu için seller_id null bırakacağız
        ]);

        $coupon = Coupon::create([
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['value'],
            'min_order_amount' => $data['min_order_amount'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'active' => $request->boolean('active', true),
            'seller_id' => null,
        ]);

        return response()->json(['success' => true, 'data' => $coupon], 201);
    }

    /**
     * Tek bir kuponu getir
     */
    public function show(Coupon $coupon)
    {
        return response()->json(['success' => true, 'data' => $coupon]);
    }

    /**
     * Kuponu güncelle
     */
    public function update(Request $request, Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        $data = $request->validate([
            'code' => ['required', 'string', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'type' => ['required', Rule::in(['fixed', 'percent', 'free_shipping'])],
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'active' => 'boolean',
        ]);

        $coupon->update([
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['value'],
            'min_order_amount' => $data['min_order_amount'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'active' => $request->boolean('active', $coupon->active),
        ]);

        return response()->json(['success' => true, 'data' => $coupon]);
    }

    /**
     * Kuponu sil
     */
    public function destroy(Coupon $coupon)
    {
        $this->authorize('delete', $coupon);
        $coupon->delete();
        return response()->json(['success' => true, 'message' => 'Coupon silindi.']);
    }

    /**
     * Aktif / pasif toggle (opsiyonel ayrı endpoint)
     */
    public function toggleActive(Coupon $coupon)
    {
        $this->authorize('toggleActive', $coupon);
        $coupon->active = !$coupon->active;
        $coupon->save();
        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => $coupon->active ? 'Kupon aktif edildi.' : 'Kupon pasif edildi.'
        ]);
    }
}
