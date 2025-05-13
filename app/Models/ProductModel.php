<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'status',
        'category_id',
        'user_id',
        'store_id'
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
}
