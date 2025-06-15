<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class SellerCouponController extends Controller
{

    use AuthorizesRequests;  // ← Bu satırı ekleyin


    // public function __construct()
    // {
    //     // Seller sadece kendi kuponlarını görecek; policy’yi buradan otomatik tetikliyoruz
    //     // $this->authorizeResource(Coupon::class, 'coupon');
    //     $this->authorizeResource(Coupon::class, 'coupon');
    // }

    /**
     * Sadece auth()->user()->id === coupon->seller_id
     * Bu policy’ye CouponPolicy::viewAny bakacak (viewAny içinde seller ise kendi kayıtlarını sorgula).
     */
    public function index()
    {
        $user = Auth::user();
        // Sadece seller’ın kendi kuponlarını listele:
        $coupons = Coupon::where('seller_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $coupons]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Coupon::class);
        $user = Auth::user();

        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => ['required', Rule::in(['fixed', 'percent', 'free_shipping'])],
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'active' => 'boolean',
        ]);

        $coupon = Coupon::create([
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['value'],
            'min_order_amount' => $data['min_order_amount'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'active' => $request->boolean('active', true),
            'seller_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'data' => $coupon], 201);
    }

    public function show(Coupon $coupon)
    {
        return response()->json(['success' => true, 'data' => $coupon]);
    }

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

    public function destroy(Coupon $coupon)
    {
        $this->authorize('delete', $coupon);
        $coupon->delete();
        return response()->json(['success' => true, 'message' => 'Coupon silindi.']);
    }

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
