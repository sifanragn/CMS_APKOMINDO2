<?php

namespace App\Models;

use App\Models\SliderImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $table = 'sliders';

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'youtube_id',
        'button_text',
        'url_link',
        'display_on_home'
    ];

    protected $casts = [
        'display_on_home' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk URL gambar
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    // Scope untuk slider yang ditampilkan di homepage
    public function scopeDisplayOnHome($query)
    {
        return $query->where('display_on_home', true);
    }

    public function extraImages()
    {
        return $this->hasMany(SliderImage::class, 'slider_id');
    }
}
