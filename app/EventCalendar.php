<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class EventCalendar extends Model
{
    protected $fillable = [
        'event_id',
        'start',
        'start_utc',
        'end',
        'end_utc',
        'utc_offset'
    ];

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

}
