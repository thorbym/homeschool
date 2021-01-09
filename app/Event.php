<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Codebyray\ReviewRateable\Contracts\ReviewRateable;
use Codebyray\ReviewRateable\Traits\ReviewRateable as ReviewRateableTrait;

class Event extends Model implements ReviewRateable
{
    use ReviewRateableTrait;

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
        'web_link',
        'youtube_link',
        'facebook_link',
        'instagram_link',
        'category_id',
        'free_content',
        'timezone',
        'average_rating',
        'image_file_id',
        'image_link',
        'video_link',
        'start_date',
        'end_date'
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function favourite()
    {
        return $this->belongsTo('App\Favourite', 'id', 'event_id')->where('user_id', Auth::check() ? Auth::user()->id : 0);
    }

    public function eventCalendars()
    {
        return $this->hasMany('App\EventCalendar')->whereNotNull('start_time');
    }

}
