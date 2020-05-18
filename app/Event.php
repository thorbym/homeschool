<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'link',
        'start_time',
        'end_time',
        'days_of_week',
        'category_id'
    ];
}
