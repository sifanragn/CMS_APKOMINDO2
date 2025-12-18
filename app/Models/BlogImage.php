<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    use HasFactory;

    protected $table = 'blog_images';

    protected $fillable = [
        'ourblog_id',
        'image',
        'title',
        'subtitle',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    public function blog()
    {
        return $this->belongsTo(OurBlog::class, 'ourblog_id');
    }

    /* =========================
       ACCESSOR
    ========================= */

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }
}
