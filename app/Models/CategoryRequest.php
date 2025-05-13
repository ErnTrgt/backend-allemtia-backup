<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryRequest extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'name', 'status'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}

