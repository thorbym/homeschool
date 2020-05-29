<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'live_web_link',
        'start_time',
        'end_time',
        'days_of_week',
        'category_id',
        'requires_supervision',
        'dfe_approved',
        'web_link',
        'minimum_age',
        'maximum_age',
        'live_youtube_link',
        'live_facebook_link',
        'live_instagram_link',
        'youtube_link',
        'facebook_link',
        'instagram_link'
    ];
}
