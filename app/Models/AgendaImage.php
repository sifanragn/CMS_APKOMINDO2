<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaImage extends Model
{
    use HasFactory;

    /**
     * Table name (opsional kalau sesuai konvensi)
     */
    protected $table = 'agenda_images';

    /**
     * Mass assignable
     */
    protected $fillable = [
        'agenda_id',
        'image',
        'title',
        'subtitle',
    ];

    /**
     * Relationship ke Agenda
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    /**
     * Accessor: full image URL
     * Biar FE langsung pakai
     */
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }
}
