<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{


    protected $fillable = [
        'title',
        'overview',
        'image_url',
        'video_url',
        'published_date'
    ];
}
