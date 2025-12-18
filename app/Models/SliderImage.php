<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    protected $table = 'slider_images'; // 🔥 WAJIB SESUAI DB

    protected $fillable = [
        'slider_id',
        'image',
        'title',
        'subtitle',
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
