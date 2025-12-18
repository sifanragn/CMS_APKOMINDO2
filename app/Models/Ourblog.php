<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ourblog extends Model
{
    protected $table = 'ourblogs';

    protected $fillable = [
        'title',
        'description',
        'image',
        'pub_date',
        'category_id',
        'waktu_baca'

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function extraImages()
    {
        return $this->hasMany(BlogImage::class, 'ourblog_id');
    }

    /* =========================
       ACCESSORS (OPTIONAL)
    ========================= */

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    protected static function booted()
{
    static::deleting(function ($blog) {
        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        foreach ($blog->extraImages as $img) {
            if (Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
        }
    });
}

}
