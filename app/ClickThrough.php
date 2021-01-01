<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClickThrough extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'clicked_link',
        'platform',
        'clicked_time',
        'clicked_timezone'
    ];
}
