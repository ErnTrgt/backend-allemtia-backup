<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = ['section_key', 'title', 'content', 'image', 'status'];

}
