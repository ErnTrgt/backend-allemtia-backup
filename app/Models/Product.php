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
        'stock',
        'status', // Aktif veya Pasif
        'category_id',
        'user_id', // Ürün sahibi
        'image', // Ürün resmi
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
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

}
