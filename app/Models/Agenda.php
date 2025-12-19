<?php

namespace App\Models;

use App\Models\AgendaImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'event_organizer',
        'location',
        'register_link',
        'youtube_link',
        'type',
        'image',
        'status',
    ];

    public function speakers()
    {
        return $this->belongsToMany(AgendaSpeaker::class, 'agenda_agenda_speaker', 'agenda_id', 'agenda_speaker_id');
    }

    public function extraImages()
    {
        return $this->hasMany(AgendaImage::class);
    }

}
