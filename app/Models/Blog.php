<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'blog_img',
        'author',
        'date',
        'status',
        'slug'
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'boolean'
    ];

    // Slug otomatik oluşturma
    public static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            $blog->slug = \Str::slug($blog->title);
        });

        static::updating(function ($blog) {
            $blog->slug = \Str::slug($blog->title);
        });
    }

    // API için özel response formatı
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'blogImg' => asset('storage/' . $this->blog_img),
            'date' => $this->date->format('d M Y'),
            'author' => $this->author,
            'content' => $this->content,
        ];
    }
}