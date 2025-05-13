<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
    //Alt kategoriler ilişkisi
    // public function subcategories()
    // {
    //     return $this->hasMany(Category::class);
    // }
}
