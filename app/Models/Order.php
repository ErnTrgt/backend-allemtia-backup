<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusChanged;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'total',
        'status',
        'payment_method',
        'payment_reference',
        'selected_bank',
        'notes',
        'tracking_number',
        'status_note',
        'cancellation_reason',
        'cancelled_at',
        'seller_note',
        'is_partially_cancelled'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'total' => 'decimal:2',
        'cancelled_at' => 'datetime'
    ];

    // Sipariş durumları
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    // İlişkiler
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Aktif siparişleri filtrele (tamamlanmamış ve iptal edilmemiş)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    // İptal edilen siparişleri filtrele
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // Sipariş durum değişikliğinde tetiklenen event
    public function updateStatus($status, $note = null)
    {
        $oldStatus = $this->status;
        $this->status = $status;
        $this->status_note = $note ?: $this->status_note;

        if ($status === self::STATUS_CANCELLED && !$this->cancelled_at) {
            $this->cancelled_at = now();
        }

        $this->save();

        // Ürün stoklarını güncelle
        if ($oldStatus !== self::STATUS_CANCELLED && $status === self::STATUS_CANCELLED) {
            $this->returnProductStock();
        }

        // Durum değişikliği e-postası gönderme
        try {
            // Mail::to($this->customer_email)->send(new OrderStatusChanged($this));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        return true;
    }

    // Sipariş verildiğinde stok miktarını güncelle
    public function reduceProductStock()
    {
        foreach ($this->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock -= $item->quantity;
                $product->save();
            }
        }
    }

    // Sipariş iptal edildiğinde stok miktarını iade et
    public function returnProductStock()
    {
        foreach ($this->items as $item) {
            // Sadece iptal edilen ürünlerin stoklarını iade et
            if (!$item->is_cancelled) {
                continue;
            }

            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }
    }

    // Tek ürün iptali işlemi
    public function cancelItem($itemId, $reason = null)
    {
        try {
            \Log::info('Cancel Item Method Called', [
                'order_id' => $this->id,
                'item_id' => $itemId,
                'reason' => $reason
            ]);

            $item = $this->items()->findOrFail($itemId);

            // Ürün zaten iptal edilmişse işlem yapma
            if ($item->is_cancelled) {
                \Log::warning('Item already cancelled', ['item_id' => $itemId]);
                return false;
            }

            // Ürünün durumunu güncelle
            $item->is_cancelled = true;
            $item->cancel_reason = $reason ?: "Müşteri tarafından iptal edildi";
            $item->cancelled_at = now();
            $item->save();

            \Log::info('Item marked as cancelled', ['item_id' => $itemId]);

            // Ürün stoğunu iade et
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
                \Log::info('Stock returned to product', [
                    'product_id' => $product->id,
                    'quantity_returned' => $item->quantity,
                    'new_stock' => $product->stock
                ]);
            }

            // Sipariş durumunu güncelle
            $activeItems = $this->items()->where('is_cancelled', false)->count();

            if ($activeItems === 0) {
                // Tüm ürünler iptal edilmişse
                $this->status = 'cancelled';
                $this->cancellation_reason = 'Tüm ürünler iptal edildi';
                $this->cancelled_at = now();
                $this->is_partially_cancelled = false; // Tamamen iptal edildiği için kısmi iptal bayrağını kaldır
                \Log::info('Order fully cancelled', ['order_id' => $this->id]);
            } else {
                // Kısmi iptal - status'u değiştirmeden sadece flag'i set et
                $this->is_partially_cancelled = true;
                \Log::info('Order partially cancelled', ['order_id' => $this->id]);
            }

            $this->save();

            return true;
        } catch (\Exception $e) {
            \Log::error('Error in cancelItem method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    // Sipariş özeti
    public function getSummaryAttribute()
    {
        $activeItems = $this->items->where('is_cancelled', false)->count();
        $cancelledItems = $this->items->where('is_cancelled', true)->count();

        return [
            'total_items' => $this->items->count(),
            'active_items' => $activeItems,
            'cancelled_items' => $cancelledItems,
            'is_fully_cancelled' => ($activeItems === 0 && $cancelledItems > 0),
            'is_partially_cancelled' => ($activeItems > 0 && $cancelledItems > 0)
        ];
    }

    // İptal edilen ürünlerin toplam tutarını hesapla
    public function getCancelledTotalAttribute()
    {
        return $this->items->where('is_cancelled', true)->sum('subtotal');
    }

    // Aktif ürünlerin toplam tutarını hesapla (iptal edilmemiş)
    public function getActiveTotalAttribute()
    {
        return $this->items->where('is_cancelled', false)->sum('subtotal');
    }

    // Güncel toplam tutarı hesapla (iptal edilmiş ürünler hariç)
    public function getCurrentTotalAttribute()
    {
        return $this->total - $this->cancelled_total;
    }

    // Toplam tutarı güncelle
    public function updateTotalAmount()
    {
        $cancelledTotal = $this->items()->where('is_cancelled', true)->sum('subtotal');
        $currentTotal = $this->total - $cancelledTotal;

        \Log::info('Updating order total', [
            'order_id' => $this->id,
            'original_total' => $this->total,
            'cancelled_total' => $cancelledTotal,
            'new_total' => $currentTotal
        ]);

        // Toplam tutarı güncelleme (isteğe bağlı)
        // $this->total = $currentTotal;
        // $this->save();

        return [
            'original_total' => $this->total,
            'cancelled_total' => $cancelledTotal,
            'current_total' => $currentTotal
        ];
    }
}
