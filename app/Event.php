<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'days_of_week',
        'minimum_age',
        'maximum_age',
        'live_web_link',
        'live_youtube_link',
        'live_facebook_link',
        'live_instagram_link',
        'requires_supervision',
        'dfe_approved',
        'web_link',
        'youtube_link',
        'facebook_link',
        'instagram_link',
        'category_id',
        'free_content',
        'timezone',
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function favourite()
    {
        return $this->belongsTo('App\Favourite', 'id', 'event_id')->where('user_id', Auth::check() ? Auth::user()->id : 0);
    }
}
