<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DpdLocation extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_active',
    ];

}

