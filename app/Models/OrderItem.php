<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'size',
        'subtotal',
        'is_cancelled',
        'cancellation_reason',
        'cancelled_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
    }

    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function cancel($reason = null)
    {
        $this->is_cancelled = true;
        $this->cancellation_reason = $reason;
        $this->cancelled_at = now();
        return $this->save();
    }
}