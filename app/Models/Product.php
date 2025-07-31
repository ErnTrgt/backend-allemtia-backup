<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model
{
    use HasFactory;

    // Toplu atama için izin verilen sütunlar
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'discount_percentage',
        'stock',
        'status', // active, pending, inactive
        'category_id',
        'user_id', // Ürün sahibi
        'image', // Ürün resmi
        'sku',
        'is_featured',
    ];

    // Ürün ile satıcı arasındaki ilişki
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Kategori ilişkisi
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Ürün sahibini getir
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Mağaza ilişkisi
    public function store()
    {
        return $this->belongsTo(Store::class, 'user_id', 'user_id');
    }
    
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }

}
