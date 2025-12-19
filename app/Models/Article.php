<?php

namespace App\Models;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'content',
        'image',
        'display',
    ];

    protected static function booted()
    {
        static::creating(function ($article) {
            $article->slug = Str::slug($article->title);
        });

        static::updating(function ($article) {
            $article->slug = Str::slug($article->title);
        });
    }

public function category()
{
    return $this->belongsTo(ArticleCategory::class, 'category_id');
}

}
