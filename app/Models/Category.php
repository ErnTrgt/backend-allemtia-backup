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

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    /**
     * Bir kategorinin başka bir kategorinin alt kategorisi olup olmadığını kontrol eder
     * 
     * @param Category|int $category Kontrol edilecek kategori veya ID'si
     * @return bool
     */
    public function isDescendantOf($category)
    {
        if (!$category) {
            return false;
        }

        $categoryId = is_object($category) ? $category->id : $category;

        // Kendi kendinin alt kategorisi olamaz
        if ($this->id === $categoryId) {
            return true;
        }

        // Mevcut kategorinin üst kategorilerini kontrol et
        $parent = $this->parent;
        while ($parent) {
            if ($parent->id === $categoryId) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }
}


