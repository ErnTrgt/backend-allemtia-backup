<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'subcategory_name',
        'status',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
