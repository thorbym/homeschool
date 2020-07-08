<?php

namespace App\Http\Controllers;

use Auth;
use \DB;
use App\Category;
use App\Event;
use App\Favourite;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::get();

        $event = null;

        $view = view('modals.eventCrud', compact('categories', 'event'))->render();

        return response()->json($view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'start_time' => 'present|string|max:5|required_with:live_youtube_link,live_facebook_link,live_instagram_link,live_web_link|nullable',
            'end_time' => 'present|string|max:5|different:start_time|required_with:live_youtube_link,live_facebook_link,live_instagram_link,live_web_link|nullable',
            'days_of_week' => 'array|required_with:live_youtube_link,live_facebook_link,live_instagram_link,live_web_link|nullable',
            'category_id' => 'required|integer',
            'live_youtube_link' => 'present|url|nullable',
            'live_facebook_link' => 'present|url|nullable',
            'live_instagram_link' => 'present|url|nullable',
            'live_web_link' => 'present|url|nullable',
            'youtube_link' => 'present|url|nullable',
            'facebook_link' => 'present|url|nullable',
            'instagram_link' => 'present|url|nullable',
            'web_link' => 'present|url|nullable',
            'minimum_age' => 'required|integer|between:0,16',
            'maximum_age' => 'required|integer|between:0,16',
            'user_id' => 'required|integer'
        ]);

        $event = new Event([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'start_time' => $request->get('start_time') ? $request->get('start_time') : null,
            'end_time' => $request->get('end_time') ? $request->get('end_time') : null,
            'days_of_week' => $request->get('days_of_week') ? json_encode($request->get('days_of_week')) : null,
            'category_id' => $request->get('category_id'),
            'requires_supervision' => $request->get('requires_supervision') ? 1 : 0,
            'dfe_approved' => $request->get('dfe_approved') ? 1 : 0,
            'live_youtube_link' => $request->get('live_youtube_link') ? $request->get('live_youtube_link') : null,
            'live_facebook_link' => $request->get('live_facebook_link') ? $request->get('live_facebook_link') : null,
            'live_instagram_link' => $request->get('live_instagram_link') ? $request->get('live_instagram_link') : null,
            'live_web_link' => $request->get('live_web_link') ? $request->get('live_web_link') : null,
            'youtube_link' => $request->get('youtube_link') ? $request->get('youtube_link') : null,
            'facebook_link' => $request->get('facebook_link') ? $request->get('facebook_link') : null,
            'instagram_link' => $request->get('instagram_link') ? $request->get('instagram_link') : null,
            'web_link' => $request->get('web_link') ? $request->get('web_link') : null,
            'minimum_age' => $request->get('minimum_age'),
            'maximum_age' => $request->get('maximum_age'),
            'free_content' => $request->get('free_content') ? 1 : 0,
            'approved' => $request->get('approved') && Auth::check() && Auth::user()->isAdmin() ? 1 : 0,
            'user_id' => $request->get('user_id') ? $request->get('user_id') : null,
        ]);
        $event->save();

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categories = Category::get();

        $query = Event::where('id', $id);

        if (Auth::check() && Auth::user()->isAdmin()) {
            //
        } else {
            $query->where('approved', '=', 1);
        }

        $event = $query->first();

        $view = view('modals.eventDetails', compact('categories', 'event'))->render();

        return response()->json($view);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCalendarEvents($filters)
    {
        $filters = json_decode($filters, true);

        $query = DB::table('events')
            ->join('categories', 'categories.id', '=', 'events.category_id');

        if (isset($filters['subjectFilter'])) {
            $categories = [];
            foreach ($filters['subjectFilter'] as $category_id => $onOrOff) {
                if ($onOrOff == "on") {
                    $categories[] += $category_id;
                }
            }
            $query->whereIn('events.category_id', $categories);
        }

        if (isset($filters['ageFilter'])) {
            $minimum_age = false;
            $maximum_age = false;
            if ($filters['ageFilter']['middleKids'] == "on" && 
                $filters['ageFilter']['littleKids'] == "on" && 
                $filters['ageFilter']['bigKids'] == "on") {
                // don't do anything - all filters means no filters!
            } else {
                if ($filters['ageFilter']['middleKids'] == "on") {
                    $minimum_age = 11;
                    $maximum_age = 7;
                }
                if ($filters['ageFilter']['littleKids'] == "on") {
                    if (!$minimum_age) {
                        $minimum_age = 6;
                    }
                }
                if ($filters['ageFilter']['bigKids'] == "on") {
                    if (!$maximum_age) {
                        $maximum_age = 12;
                    }
                }
                if ($minimum_age) {
                    $query->where('events.minimum_age', '<=', $minimum_age);
                }
                if ($maximum_age) {
                    $query->where('events.maximum_age', '>=', $maximum_age);
                }
            }
        }

        if (isset($filters['otherFilters'])) {
            foreach ($filters['otherFilters'] as $filterType => $onOrOff) {
                if ($onOrOff == "on") {
                    $query->where('events.' . $filterType, 1);
                }
            }
        }

        if (isset($filters['showFavourites']) && Auth::check()) {
            $query->join('favourites', function($join) {
                $join->on('favourites.event_id', '=', 'events.id');
                $join->where('favourites.user_id', '=', Auth::user()->id);
            });
        }
        $query->select(
            'events.id',
            DB::raw('(case when events.free_content = 0 then CONCAT(events.title, " [PAID]") else events.title end) as title'),
            'events.description',
            'events.start_time AS startTime',
            'events.end_time AS endTime',
            'events.days_of_week AS daysOfWeek',
            'events.minimum_age',
            'events.maximum_age',
            'events.dfe_approved',
            'events.requires_supervision',
            'events.free_content',
            'categories.category',
            'categories.colour',
            'categories.font_colour'
        );
        $query->whereNotNull('events.start_time');
        $query->where('events.approved', '=', 1);

        $events = $query->get();

        return response()->json($events);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getListEvents($filters)
    {
        $filters = json_decode($filters, true);

        $query = DB::table('events')
            ->join('categories', 'categories.id', '=', 'events.category_id');

        if (Auth::check()) {
            $query->leftJoin('favourites', function($join) {
                $join->on('favourites.event_id', '=', 'events.id');
                $join->where('favourites.user_id', '=', Auth::user()->id);
            });
        }

        if (isset($filters['subjectFilter'])) {
            $categories = [];
            foreach ($filters['subjectFilter'] as $category_id => $onOrOff) {
                if ($onOrOff == "on") {
                    $categories[] += $category_id;
                }
            }
            $query->whereIn('events.category_id', $categories);
        }

        if (isset($filters['ageFilter'])) {
            $minimum_age = false;
            $maximum_age = false;
            if ($filters['ageFilter']['middleKids'] == "on" && 
                $filters['ageFilter']['littleKids'] == "on" && 
                $filters['ageFilter']['bigKids'] == "on") {
                // don't do anything - all filters means no filters!
            } else {
                if ($filters['ageFilter']['middleKids'] == "on") {
                    $minimum_age = 11;
                    $maximum_age = 7;
                }
                if ($filters['ageFilter']['littleKids'] == "on") {
                    if (!$minimum_age) {
                        $minimum_age = 6;
                    }
                }
                if ($filters['ageFilter']['bigKids'] == "on") {
                    if (!$maximum_age) {
                        $maximum_age = 12;
                    }
                }
                if ($minimum_age) {
                    $query->where('events.minimum_age', '<=', $minimum_age);
                }
                if ($maximum_age) {
                    $query->where('events.maximum_age', '>=', $maximum_age);
                }
            }
        }

        if (isset($filters['otherFilters'])) {
            foreach ($filters['otherFilters'] as $filterType => $onOrOff) {
                if ($onOrOff == "on") {
                    $query->where('events.' . $filterType, 1);
                }
            }
        }

        if (isset($filters['showFavourites']) && Auth::check()) {
            $query->whereNotNull('favourites.id');
        }

        $query->select(
            'events.id',
            DB::raw('(case when events.free_content = 0 then CONCAT(events.title, " [PAID]") else events.title end) as title'),
            'events.description',
            'events.start_time AS startTime',
            'events.end_time AS endTime',
            'events.days_of_week AS daysOfWeek',
            'events.minimum_age',
            'events.maximum_age',
            'events.dfe_approved',
            'events.requires_supervision',
            'events.free_content',
            'categories.category',
            'categories.colour',
            'categories.font_colour',
            Auth::check() ? DB::raw('(case when favourites.id is null then 0 else favourites.id end) as favourite_id') : DB::raw('0 AS favourite_id')
        );
        $query->where('events.approved', '=', 1);
        $events = $query->get();

        $view = view('layouts.table', compact('events'))->render();

        return response()->json($view);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::get();

        $query = Event::where('id', $id);

        if (Auth::check() && Auth::user()->isAdmin()) {
            $event = $query->first();
            $view = view('modals.eventCrud', compact('categories', 'event'))->render();

        } else {
            $query->where('approved', '=', 1);
            $event = $query->first();
            // just belt and braces to make sure that non auth user cannot edit
            $view = view('modals.eventDetails', compact('categories', 'event'))->render();
        }

        return response()->json($view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'start_time' => 'present|string|max:5|required_with:live_youtube_link,live_facebook_link,live_instagram_link,live_web_link|nullable',
            'end_time' => 'present|string|max:5|different:start_time|required_with:live_youtube_link,live_facebook_link,live_instagram_link,live_web_link|nullable',
            'days_of_week' => 'array|required_with:live_youtube_link,live_facebook_link,live_instagram_link,live_web_link|nullable',
            'category_id' => 'required|integer',
            'live_youtube_link' => 'present|url|nullable',
            'live_facebook_link' => 'present|url|nullable',
            'live_instagram_link' => 'present|url|nullable',
            'live_web_link' => 'present|url|nullable',
            'youtube_link' => 'present|url|nullable',
            'facebook_link' => 'present|url|nullable',
            'instagram_link' => 'present|url|nullable',
            'web_link' => 'present|url|nullable',
            'minimum_age' => 'required|integer|between:0,16',
            'maximum_age' => 'required|integer|between:0,16',
        ]);

        $event = Event::find($id);

        $event->title = $request->get('title');
        $event->description = $request->get('description');
        $event->start_time = $request->get('start_time') ? $request->get('start_time') : null;
        $event->end_time = $request->get('end_time') ? $request->get('end_time') : null;
        $event->days_of_week = $request->get('days_of_week') ? json_encode($request->get('days_of_week')) : null;
        $event->category_id = $request->get('category_id');
        $event->requires_supervision = $request->get('requires_supervision') ? 1 : 0;
        $event->dfe_approved = $request->get('dfe_approved') ? 1 : 0;
        $event->live_youtube_link = $request->get('live_youtube_link') ? $request->get('live_youtube_link') : null;
        $event->live_facebook_link = $request->get('live_facebook_link') ? $request->get('live_facebook_link') : null;
        $event->live_instagram_link = $request->get('live_instagram_link') ? $request->get('live_instagram_link') : null;
        $event->live_web_link = $request->get('live_web_link') ? $request->get('live_web_link') : null;
        $event->youtube_link = $request->get('youtube_link') ? $request->get('youtube_link') : null;
        $event->facebook_link = $request->get('facebook_link') ? $request->get('facebook_link') : null;
        $event->instagram_link = $request->get('instagram_link') ? $request->get('instagram_link') : null;
        $event->web_link = $request->get('web_link') ? $request->get('web_link') : null;
        $event->minimum_age = $request->get('minimum_age');
        $event->maximum_age = $request->get('maximum_age');
        $event->free_content = $request->get('free_content') ? 1 : 0;
        $event->approved = $request->get('approved') && Auth::check() && Auth::user()->isAdmin() ? 1 : 0;

        $event->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            $event = Event::destroy($id);
        }

        return redirect()->back();
    }
}
