<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'once_per_user',
        'starts_at',
        'expires_at',
        'active',
        'seller_id',
        'used_count',          // yeni sütun
    ];

    protected $casts = [
        'once_per_user' => 'boolean',
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Bu kupon hangi ürünlere geçerli?
     */

    protected $dates = ['starts_at', 'expires_at'];
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'coupon_product',
            'coupon_id',
            'product_id'
        );
    }

    /**
     * Hangi kullanıcılar bu kuponu kullandı?
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withTimestamps();
    }

    /**
     * Eğer bir satıcıya ait kuponsa, o satıcı
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Kupon hâlâ geçerli mi?
     */
    public function isValid(): bool
    {
        if ($this->active === false)
            return false;
        if ($this->starts_at && $this->starts_at->isFuture())
            return false;
        if ($this->expires_at && $this->expires_at->isPast())
            return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit)
            return false;
        return $this->active;
    }

    /**
     * Sepet tutarına göre indirim hesapla
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percent') {
            $discount = round($amount * ($this->value / 100), 2);
        } elseif ($this->type === 'fixed') {
            $discount = round($this->value, 2);
        } else {
            // free_shipping veya diğer tipler için 0
            $discount = 0;
        }

        // İndirim sipariş tutarını aşmasın
        return min($discount, $amount);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
