<?php

namespace App\Http\Controllers;

use Auth;
use \DB;
use App\Category;
use App\Event;
use App\EventCalendar;
use App\Favourite;
use DateTime;
use Illuminate\Http\Request;
use Spatie\CalendarLinks\Link;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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

        $timezones = self::getTimezones();

        return view('events.crud', compact('categories', 'event', 'timezones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            'timezone' => 'present|string|nullable',
        ], [
            'required' => 'The :attribute field is required',
            'url' => 'The :attribute link is not a proper url (ie. "https://www.google.com/")'
        ]);

        // Check validation failure
        if ($validator->fails()) {
           return back()->withInput()->withErrors($validator);
        }

        // Check validation success
        if ($validator->passes()) {
            $imageFileId = false;
            if ($request->hasFile('image')) {
                $imageFileId = Storage::disk('public')->put('image', $request->file('image'));
            }

            $event = new Event([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'start_time' => $request->get('start_time') ? $request->get('start_time') : null,
                'end_time' => $request->get('end_time') ? $request->get('end_time') : null,
                'days_of_week' => $request->get('days_of_week') ? json_encode($request->get('days_of_week')) : null,
                'category_id' => $request->get('category_id'),
                'requires_supervision' => $request->get('requires_supervision') ? 1 : 0,
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
                'free_content' => $request->get('free_content') ?   1 : 0,
                'timezone' => $request->get('timezone') ? $request->get('timezone') : null,
                'image_file_id' => $imageFileId ? $imageFileId : null,
            ]);
            $event->save();

            // if the event has a start time, then we need to add an event_calendar for it too
            if ($request->get('start_time')) {
                self::createEventCalendar($event->id, $request->get('days_of_week'), $request->get('start_time'), $request->get('end_time'), $request->get('timezone'));
            }

            return redirect()->back();
        }

    }

    public static function createEventCalendar($eventId, $daysOfWeek, $startTime, $endTime, $timezone)
    {
        // start from today
        $date = date('Y-m-d');

        // loop around 600 "days"
        for ($i = 0; $i < 600; $i++) {

            // get the "day of week" number of this particular date
            $dayNumber = date('N', strtotime($date));

            // if this event has an occurence on this day number, then insert a row
            if (in_array($dayNumber, $daysOfWeek)) {

                // calc utc offset by making a UTC DateTime using the datetime in question, and comparing it with the timezone that's passed in (in this case, will always be London)
                $timezoneCheck = new \DateTime($date . ' ' . $startTime, new \DateTimeZone($timezone));
                $offset = $timezoneCheck->getOffset();

                // $utc_offset needs to be Z for zero, or convert the seconds (eg. 01:00)
                if ($offset === 0) {
                    $utcOffset = "Z";
                } else if ($offset > 0) {
                    $utcOffset = "+" . gmdate("H:i", $offset);
                } else {
                    $offset = -$offset;
                    $utcOffset = "-" . gmdate("H:i", $offset);
                }

                $saveArr = [
                    'event_id' => $eventId,
                    'start' => $date . ' ' . $startTime,
                    'start_utc' => gmdate('Y-m-d H:i', strtotime($date . 'T' . $startTime . $utcOffset)),
                    'end' => $date . ' ' . $endTime,
                    'end_utc' => gmdate('Y-m-d H:i', strtotime($date . 'T' . $endTime . $utcOffset)),
                    'utc_offset' => $utcOffset
                ];

                EventCalendar::insert($saveArr);
            }

            // increment the date by a day, and run the whole thing again
            $date = date('Y-m-d', strtotime($date . ' +1 day'));

            // stop after two years
            if ($date >= "2022-12-31") {
                break;
            }
        }
    }

    /**
     * This comes from the calendar, so we know it has a specific calendar entry
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEventFromCalendar($eventCalendarId)
    {
        $categories = Category::get();

        $eventCalendar = EventCalendar::where('id', $eventCalendarId)->first();
        $from = DateTime::createFromFormat('Y-m-d H:i:s', $eventCalendar->start);
        $to = DateTime::createFromFormat('Y-m-d H:i:s', $eventCalendar->end);

        $event = $eventCalendar->event;
        $linkToCalendar = (object) [
            'google' => Link::create($event->title, $from, $to)->description($event->description)->google(),
            'ics' => Link::create($event->title, $from, $to)->description($event->description)->ics(),
            'yahoo' => Link::create($event->title, $from, $to)->description($event->description)->yahoo(),
            'webOutlook' => Link::create($event->title, $from, $to)->description($event->description)->webOutlook()
        ];

        $fromCalendar = true;

        return view('events.details', compact('categories', 'event', 'eventCalendar', 'fromCalendar', 'linkToCalendar'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEventFromList($id)
    {

        $categories = Category::get();

        $event = Event::where('id', $id)->first();
        $eventCalendar = false;
        $linkToCalendar = [];
        if ($event->start_time) {
            $eventCalendar = EventCalendar::where('event_id', $id)
                ->where('end_utc', '>', gmdate('Y-m-d H:i'))
                ->orderBy('end_utc', 'ASC')
                ->first();

            $from = DateTime::createFromFormat('Y-m-d H:i:s', $eventCalendar->start);
            $to = DateTime::createFromFormat('Y-m-d H:i:s', $eventCalendar->end);

            $linkToCalendar = (object) [
                'google' => Link::create($event->title, $from, $to)->description($event->description)->google(),
                'ics' => Link::create($event->title, $from, $to)->description($event->description)->ics(),
                'yahoo' => Link::create($event->title, $from, $to)->description($event->description)->yahoo(),
                'webOutlook' => Link::create($event->title, $from, $to)->description($event->description)->webOutlook()
            ];
        }

        $fromCalendar = false;

        return view('events.details', compact('categories', 'event', 'eventCalendar', 'fromCalendar', 'linkToCalendar'));
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

        $query = DB::table('event_calendars')
            ->join('events', 'events.id', '=', 'event_calendars.event_id')
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
            'event_calendars.id AS event_calendar_id',
            'events.title',
            'events.description',
            DB::raw('CONCAT(REPLACE(event_calendars.start," ","T"), utc_offset) as start'),
            DB::raw('CONCAT(REPLACE(event_calendars.end," ","T"), utc_offset) as end'),
            'events.minimum_age',
            'events.maximum_age',
            'events.requires_supervision',
            'events.free_content',
            'events.average_rating',
            'categories.category',
            'categories.colour',
            'categories.font_colour'
        );

        $query->where('event_calendars.start_utc', '>', gmdate('Y-m-d H:i', strtotime($filters['from'])))
            ->where('event_calendars.end_utc', '<', gmdate('Y-m-d H:i', strtotime($filters['to'])));
        
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

        if (isset($filters['ratingsFilter'])) {
            foreach ($filters['ratingsFilter'] as $rating => $onOrOff) {
                if ($onOrOff == "on") {
                    $query->where('events.average_rating', '>=', $rating);
                    break;
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
            'events.title',
            'events.description',
            'events.minimum_age',
            'events.maximum_age',
            'events.requires_supervision',
            'events.free_content',
            'events.average_rating',
            'events.image_file_id',
            'categories.category',
            'categories.colour',
            'categories.font_colour',
            Auth::check() ? DB::raw('(case when favourites.id is null then 0 else favourites.id end) as favourite_id') : DB::raw('0 AS favourite_id')
        );

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
    public function editEventFromCalendar($eventCalendarId)
    {
        $categories = Category::get();

        $eventCalendar = EventCalendar::where('id', $eventCalendarId)->first();

        $event = $eventCalendar->event;

        if (Auth::check() && Auth::user()->isAdmin()) {
            $view = view('modals.eventCrud', compact('categories', 'event'))->render();

        } else {
            // just belt and braces to make sure that non auth user cannot edit
            $view = view('modals.eventDetails', compact('categories', 'event'))->render();

        }

        return response()->json($view);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEventFromList($id)
    {
        $categories = Category::get();

        $event = Event::where('id', $id)->first();
        
        if (Auth::check() && Auth::user()->isAdmin()) {
            $view = view('modals.eventCrud', compact('categories', 'event'))->render();

        } else {
            // just belt and braces to make sure that non auth user cannot edit
            $view = view('modals.eventDetails', compact('categories', 'event'))->render();

        }

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

        $event = Event::where('id', $id)->first();

        $timezones = self::getTimezones();

        return view('events.crud', compact('categories', 'event', 'timezones'));
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
            'timezone' => 'present|string|nullable',
        ]);

        $event = Event::find($id);

        $oldStartTime = $event->start_time;
        $oldEndTime = $event->end_time;
        $oldDaysOfWeek = $event->days_of_week;

        $event->title = $request->get('title');
        $event->description = $request->get('description');
        $event->start_time = $request->get('start_time') ? $request->get('start_time') : null;
        $event->end_time = $request->get('end_time') ? $request->get('end_time') : null;
        $event->days_of_week = $request->get('days_of_week') ? json_encode($request->get('days_of_week')) : null;
        $event->category_id = $request->get('category_id');
        $event->requires_supervision = $request->get('requires_supervision') ? 1 : 0;
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
        $event->timezone = $request->get('timezone') ? $request->get('timezone') : null;

        $event->save();

        // if old start time and not new one, delete calendar
        if ($oldStartTime && empty($request->input('start_time'))) {
            EventCalendar::where('event_id', $id)->delete();
        }

        // if old start time and new one, and they are the same, do nothing
        if ($oldStartTime && !empty($request->input('start_time')) && $oldStartTime == $request->input('start_time') && $oldEndTime == $request->input('end_time') && $oldDaysOfWeek == json_encode($request->input('days_of_week'))) {
            // do nothing
        }


        // if there is an old start time and a new one, and they are different in some way, delete calendar and create new one
        if ($oldStartTime && !empty($request->input('start_time')) && ($oldStartTime != $request->input('start_time') || $oldEndTime != $request->input('end_time') || $oldDaysOfWeek != json_encode($request->input('days_of_week')))) {
            EventCalendar::where('event_id', $id)->delete();

            self::createEventCalendar($id, $request->get('days_of_week'), $request->get('start_time'), $request->get('end_time'), $request->get('timezone'));
        }

        // if there is no old start date and there is a new one, build a calendar
        if (!$oldStartTime && !empty($request->input('start_time'))) {
            self::createEventCalendar($id, $request->get('days_of_week'), $request->get('start_time'), $request->get('end_time'), $request->get('timezone'));
        }

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
        // destroy the event
        Event::destroy($id);
        // also destroy any calendar events too
        EventCalendar::where('event_id', $id)->delete();

        return redirect()->back();
    }

    public function storeReview(Request $request, $event_id)
    {
        $event = Event::where('id', $event_id)->first();
        $rating = $event->rating([
            'title' => $request->get('title'),
            'body' => $request->get('review'),
            'customer_service_rating' => null,
            'quality_rating' => null,
            'friendly_rating' => null,
            'pricing_rating' => null,
            'rating' => $request->get('rating'),
            'recommend' => 'Yes'
        ], Auth::user());

        $event->average_rating = $event->averageRating()[0];
        $event->save();

        return redirect()->back();
    }

    public static function getTimezones()
    {
        return [
            "US (Common)" => [
                "America/Puerto_Rico" => "Puerto Rico (Atlantic)",
                "America/New_York" => "New York (Eastern)",
                "America/Chicago" => "Chicago (Central)",
                "America/Denver" => "Denver (Mountain)",
                "America/Phoenix" => "Phoenix (MST)",
                "America/Los_Angeles" => "Los Angeles (Pacific)",
                "Europe/London" => "London",
                "Europe/Berlin" => "Berlin",
                "Europe/Brussels" => "Brussels",
                "Asia/Calcutta" => "Calcutta",
                "Africa/Nairobi" => "Nairobi",
                "Australia/Sydney" => "Sydney",
            ],

            "America" => [
                "America/Adak" => "Adak",
                "America/Anchorage" => "Anchorage",
                "America/Anguilla" => "Anguilla",
                "America/Antigua" => "Antigua",
                "America/Araguaina" => "Araguaina",
                "America/Argentina/Buenos_Aires" => "Argentina - Buenos Aires",
                "America/Argentina/Catamarca" => "Argentina - Catamarca",
                "America/Argentina/ComodRivadavia" => "Argentina - ComodRivadavia",
                "America/Argentina/Cordoba" => "Argentina - Cordoba",
                "America/Argentina/Jujuy" => "Argentina - Jujuy",
                "America/Argentina/La_Rioja" => "Argentina - La Rioja",
                "America/Argentina/Mendoza" => "Argentina - Mendoza",
                "America/Argentina/Rio_Gallegos" => "Argentina - Rio Gallegos",
                "America/Argentina/Salta" => "Argentina - Salta",
                "America/Argentina/San_Juan" => "Argentina - San Juan",
                "America/Argentina/San_Luis" => "Argentina - San Luis",
                "America/Argentina/Tucuman" => "Argentina - Tucuman",
                "America/Argentina/Ushuaia" => "Argentina - Ushuaia",
                "America/Aruba" => "Aruba",
                "America/Asuncion" => "Asuncion",
                "America/Atikokan" => "Atikokan",
                "America/Atka" => "Atka",
                "America/Bahia" => "Bahia",
                "America/Barbados" => "Barbados",
                "America/Belem" => "Belem",
                "America/Belize" => "Belize",
                "America/Blanc-Sablon" => "Blanc-Sablon",
                "America/Boa_Vista" => "Boa Vista",
                "America/Bogota" => "Bogota",
                "America/Boise" => "Boise",
                "America/Buenos_Aires" => "Buenos Aires",
                "America/Cambridge_Bay" => "Cambridge Bay",
                "America/Campo_Grande" => "Campo Grande",
                "America/Cancun" => "Cancun",
                "America/Caracas" => "Caracas",
                "America/Catamarca" => "Catamarca",
                "America/Cayenne" => "Cayenne",
                "America/Cayman" => "Cayman",
                "America/Chicago" => "Chicago",
                "America/Chihuahua" => "Chihuahua",
                "America/Coral_Harbour" => "Coral Harbour",
                "America/Cordoba" => "Cordoba",
                "America/Costa_Rica" => "Costa Rica",
                "America/Cuiaba" => "Cuiaba",
                "America/Curacao" => "Curacao",
                "America/Danmarkshavn" => "Danmarkshavn",
                "America/Dawson" => "Dawson",
                "America/Dawson_Creek" => "Dawson Creek",
                "America/Denver" => "Denver",
                "America/Detroit" => "Detroit",
                "America/Dominica" => "Dominica",
                "America/Edmonton" => "Edmonton",
                "America/Eirunepe" => "Eirunepe",
                "America/El_Salvador" => "El Salvador",
                "America/Ensenada" => "Ensenada",
                "America/Fortaleza" => "Fortaleza",
                "America/Fort_Wayne" => "Fort Wayne",
                "America/Glace_Bay" => "Glace Bay",
                "America/Godthab" => "Godthab",
                "America/Goose_Bay" => "Goose Bay",
                "America/Grand_Turk" => "Grand Turk",
                "America/Grenada" => "Grenada",
                "America/Guadeloupe" => "Guadeloupe",
                "America/Guatemala" => "Guatemala",
                "America/Guayaquil" => "Guayaquil",
                "America/Guyana" => "Guyana",
                "America/Halifax" => "Halifax",
                "America/Havana" => "Havana",
                "America/Hermosillo" => "Hermosillo",
                "America/Indiana/Indianapolis" => "Indiana - Indianapolis",
                "America/Indiana/Knox" => "Indiana - Knox",
                "America/Indiana/Marengo" => "Indiana - Marengo",
                "America/Indiana/Petersburg" => "Indiana - Petersburg",
                "America/Indiana/Tell_City" => "Indiana - Tell City",
                "America/Indiana/Vevay" => "Indiana - Vevay",
                "America/Indiana/Vincennes" => "Indiana - Vincennes",
                "America/Indiana/Winamac" => "Indiana - Winamac",
                "America/Indianapolis" => "Indianapolis",
                "America/Inuvik" => "Inuvik",
                "America/Iqaluit" => "Iqaluit",
                "America/Jamaica" => "Jamaica",
                "America/Jujuy" => "Jujuy",
                "America/Juneau" => "Juneau",
                "America/Kentucky/Louisville" => "Kentucky - Louisville",
                "America/Kentucky/Monticello" => "Kentucky - Monticello",
                "America/Knox_IN" => "Knox IN",
                "America/La_Paz" => "La Paz",
                "America/Lima" => "Lima",
                "America/Los_Angeles" => "Los Angeles",
                "America/Louisville" => "Louisville",
                "America/Maceio" => "Maceio",
                "America/Managua" => "Managua",
                "America/Manaus" => "Manaus",
                "America/Marigot" => "Marigot",
                "America/Martinique" => "Martinique",
                "America/Matamoros" => "Matamoros",
                "America/Mazatlan" => "Mazatlan",
                "America/Mendoza" => "Mendoza",
                "America/Menominee" => "Menominee",
                "America/Merida" => "Merida",
                "America/Mexico_City" => "Mexico City",
                "America/Miquelon" => "Miquelon",
                "America/Moncton" => "Moncton",
                "America/Monterrey" => "Monterrey",
                "America/Montevideo" => "Montevideo",
                "America/Montreal" => "Montreal",
                "America/Montserrat" => "Montserrat",
                "America/Nassau" => "Nassau",
                "America/New_York" => "New York",
                "America/Nipigon" => "Nipigon",
                "America/Nome" => "Nome",
                "America/Noronha" => "Noronha",
                "America/North_Dakota/Center" => "North Dakota - Center",
                "America/North_Dakota/New_Salem" => "North Dakota - New Salem",
                "America/Ojinaga" => "Ojinaga",
                "America/Panama" => "Panama",
                "America/Pangnirtung" => "Pangnirtung",
                "America/Paramaribo" => "Paramaribo",
                "America/Phoenix" => "Phoenix",
                "America/Port-au-Prince" => "Port-au-Prince",
                "America/Porto_Acre" => "Porto Acre",
                "America/Port_of_Spain" => "Port of Spain",
                "America/Porto_Velho" => "Porto Velho",
                "America/Puerto_Rico" => "Puerto Rico",
                "America/Rainy_River" => "Rainy River",
                "America/Rankin_Inlet" => "Rankin Inlet",
                "America/Recife" => "Recife",
                "America/Regina" => "Regina",
                "America/Resolute" => "Resolute",
                "America/Rio_Branco" => "Rio Branco",
                "America/Rosario" => "Rosario",
                "America/Santa_Isabel" => "Santa Isabel",
                "America/Santarem" => "Santarem",
                "America/Santiago" => "Santiago",
                "America/Santo_Domingo" => "Santo Domingo",
                "America/Sao_Paulo" => "Sao Paulo",
                "America/Scoresbysund" => "Scoresbysund",
                "America/Shiprock" => "Shiprock",
                "America/St_Barthelemy" => "St Barthelemy",
                "America/St_Johns" => "St Johns",
                "America/St_Kitts" => "St Kitts",
                "America/St_Lucia" => "St Lucia",
                "America/St_Thomas" => "St Thomas",
                "America/St_Vincent" => "St Vincent",
                "America/Swift_Current" => "Swift Current",
                "America/Tegucigalpa" => "Tegucigalpa",
                "America/Thule" => "Thule",
                "America/Thunder_Bay" => "Thunder Bay",
                "America/Tijuana" => "Tijuana",
                "America/Toronto" => "Toronto",
                "America/Tortola" => "Tortola",
                "America/Vancouver" => "Vancouver",
                "America/Virgin" => "Virgin",
                "America/Whitehorse" => "Whitehorse",
                "America/Winnipeg" => "Winnipeg",
                "America/Yakutat" => "Yakutat",
                "America/Yellowknife" => "Yellowknife",
            ],

            "Europe" => [
                "Europe/Amsterdam" => "Amsterdam",
                "Europe/Andorra" => "Andorra",
                "Europe/Athens" => "Athens",
                "Europe/Belfast" => "Belfast",
                "Europe/Belgrade" => "Belgrade",
                "Europe/Berlin" => "Berlin",
                "Europe/Bratislava" => "Bratislava",
                "Europe/Brussels" => "Brussels",
                "Europe/Bucharest" => "Bucharest",
                "Europe/Budapest" => "Budapest",
                "Europe/Chisinau" => "Chisinau",
                "Europe/Copenhagen" => "Copenhagen",
                "Europe/Dublin" => "Dublin",
                "Europe/Gibraltar" => "Gibraltar",
                "Europe/Guernsey" => "Guernsey",
                "Europe/Helsinki" => "Helsinki",
                "Europe/Isle_of_Man" => "Isle of Man",
                "Europe/Istanbul" => "Istanbul",
                "Europe/Jersey" => "Jersey",
                "Europe/Kaliningrad" => "Kaliningrad",
                "Europe/Kiev" => "Kiev",
                "Europe/Lisbon" => "Lisbon",
                "Europe/Ljubljana" => "Ljubljana",
                "Europe/London" => "London",
                "Europe/Luxembourg" => "Luxembourg",
                "Europe/Madrid" => "Madrid",
                "Europe/Malta" => "Malta",
                "Europe/Mariehamn" => "Mariehamn",
                "Europe/Minsk" => "Minsk",
                "Europe/Monaco" => "Monaco",
                "Europe/Moscow" => "Moscow",
                "Europe/Nicosia" => "Nicosia",
                "Europe/Oslo" => "Oslo",
                "Europe/Paris" => "Paris",
                "Europe/Podgorica" => "Podgorica",
                "Europe/Prague" => "Prague",
                "Europe/Riga" => "Riga",
                "Europe/Rome" => "Rome",
                "Europe/Samara" => "Samara",
                "Europe/San_Marino" => "San Marino",
                "Europe/Sarajevo" => "Sarajevo",
                "Europe/Simferopol" => "Simferopol",
                "Europe/Skopje" => "Skopje",
                "Europe/Sofia" => "Sofia",
                "Europe/Stockholm" => "Stockholm",
                "Europe/Tallinn" => "Tallinn",
                "Europe/Tirane" => "Tirane",
                "Europe/Tiraspol" => "Tiraspol",
                "Europe/Uzhgorod" => "Uzhgorod",
                "Europe/Vaduz" => "Vaduz",
                "Europe/Vatican" => "Vatican",
                "Europe/Vienna" => "Vienna",
                "Europe/Vilnius" => "Vilnius",
                "Europe/Volgograd" => "Volgograd",
                "Europe/Warsaw" => "Warsaw",
                "Europe/Zagreb" => "Zagreb",
                "Europe/Zaporozhye" => "Zaporozhye",
                "Europe/Zurich" => "Zurich",
            ],
            
            "Asia" => [
                "Asia/Aden" => "Aden",
                "Asia/Almaty" => "Almaty",
                "Asia/Amman" => "Amman",
                "Asia/Anadyr" => "Anadyr",
                "Asia/Aqtau" => "Aqtau",
                "Asia/Aqtobe" => "Aqtobe",
                "Asia/Ashgabat" => "Ashgabat",
                "Asia/Ashkhabad" => "Ashkhabad",
                "Asia/Baghdad" => "Baghdad",
                "Asia/Bahrain" => "Bahrain",
                "Asia/Baku" => "Baku",
                "Asia/Bangkok" => "Bangkok",
                "Asia/Beirut" => "Beirut",
                "Asia/Bishkek" => "Bishkek",
                "Asia/Brunei" => "Brunei",
                "Asia/Calcutta" => "Calcutta",
                "Asia/Choibalsan" => "Choibalsan",
                "Asia/Chongqing" => "Chongqing",
                "Asia/Chungking" => "Chungking",
                "Asia/Colombo" => "Colombo",
                "Asia/Dacca" => "Dacca",
                "Asia/Damascus" => "Damascus",
                "Asia/Dhaka" => "Dhaka",
                "Asia/Dili" => "Dili",
                "Asia/Dubai" => "Dubai",
                "Asia/Dushanbe" => "Dushanbe",
                "Asia/Gaza" => "Gaza",
                "Asia/Harbin" => "Harbin",
                "Asia/Ho_Chi_Minh" => "Ho Chi Minh",
                "Asia/Hong_Kong" => "Hong Kong",
                "Asia/Hovd" => "Hovd",
                "Asia/Irkutsk" => "Irkutsk",
                "Asia/Istanbul" => "Istanbul",
                "Asia/Jakarta" => "Jakarta",
                "Asia/Jayapura" => "Jayapura",
                "Asia/Jerusalem" => "Jerusalem",
                "Asia/Kabul" => "Kabul",
                "Asia/Kamchatka" => "Kamchatka",
                "Asia/Karachi" => "Karachi",
                "Asia/Kashgar" => "Kashgar",
                "Asia/Kathmandu" => "Kathmandu",
                "Asia/Katmandu" => "Katmandu",
                "Asia/Kolkata" => "Kolkata",
                "Asia/Krasnoyarsk" => "Krasnoyarsk",
                "Asia/Kuala_Lumpur" => "Kuala Lumpur",
                "Asia/Kuching" => "Kuching",
                "Asia/Kuwait" => "Kuwait",
                "Asia/Macao" => "Macao",
                "Asia/Macau" => "Macau",
                "Asia/Magadan" => "Magadan",
                "Asia/Makassar" => "Makassar",
                "Asia/Manila" => "Manila",
                "Asia/Muscat" => "Muscat",
                "Asia/Nicosia" => "Nicosia",
                "Asia/Novokuznetsk" => "Novokuznetsk",
                "Asia/Novosibirsk" => "Novosibirsk",
                "Asia/Omsk" => "Omsk",
                "Asia/Oral" => "Oral",
                "Asia/Phnom_Penh" => "Phnom Penh",
                "Asia/Pontianak" => "Pontianak",
                "Asia/Pyongyang" => "Pyongyang",
                "Asia/Qatar" => "Qatar",
                "Asia/Qyzylorda" => "Qyzylorda",
                "Asia/Rangoon" => "Rangoon",
                "Asia/Riyadh" => "Riyadh",
                "Asia/Saigon" => "Saigon",
                "Asia/Sakhalin" => "Sakhalin",
                "Asia/Samarkand" => "Samarkand",
                "Asia/Seoul" => "Seoul",
                "Asia/Shanghai" => "Shanghai",
                "Asia/Singapore" => "Singapore",
                "Asia/Taipei" => "Taipei",
                "Asia/Tashkent" => "Tashkent",
                "Asia/Tbilisi" => "Tbilisi",
                "Asia/Tehran" => "Tehran",
                "Asia/Tel_Aviv" => "Tel Aviv",
                "Asia/Thimbu" => "Thimbu",
                "Asia/Thimphu" => "Thimphu",
                "Asia/Tokyo" => "Tokyo",
                "Asia/Ujung_Pandang" => "Ujung Pandang",
                "Asia/Ulaanbaatar" => "Ulaanbaatar",
                "Asia/Ulan_Bator" => "Ulan Bator",
                "Asia/Urumqi" => "Urumqi",
                "Asia/Vientiane" => "Vientiane",
                "Asia/Vladivostok" => "Vladivostok",
                "Asia/Yakutsk" => "Yakutsk",
                "Asia/Yekaterinburg" => "Yekaterinburg",
                "Asia/Yerevan" => "Yerevan",
            ],

            "Africa" => [
                "Africa/Abidjan" => "Abidjan",
                "Africa/Accra" => "Accra",
                "Africa/Addis_Ababa" => "Addis Ababa",
                "Africa/Algiers" => "Algiers",
                "Africa/Asmara" => "Asmara",
                "Africa/Asmera" => "Asmera",
                "Africa/Bamako" => "Bamako",
                "Africa/Bangui" => "Bangui",
                "Africa/Banjul" => "Banjul",
                "Africa/Bissau" => "Bissau",
                "Africa/Blantyre" => "Blantyre",
                "Africa/Brazzaville" => "Brazzaville",
                "Africa/Bujumbura" => "Bujumbura",
                "Africa/Cairo" => "Cairo",
                "Africa/Casablanca" => "Casablanca",
                "Africa/Ceuta" => "Ceuta",
                "Africa/Conakry" => "Conakry",
                "Africa/Dakar" => "Dakar",
                "Africa/Dar_es_Salaam" => "Dar es Salaam",
                "Africa/Djibouti" => "Djibouti",
                "Africa/Douala" => "Douala",
                "Africa/El_Aaiun" => "El Aaiun",
                "Africa/Freetown" => "Freetown",
                "Africa/Gaborone" => "Gaborone",
                "Africa/Harare" => "Harare",
                "Africa/Johannesburg" => "Johannesburg",
                "Africa/Kampala" => "Kampala",
                "Africa/Khartoum" => "Khartoum",
                "Africa/Kigali" => "Kigali",
                "Africa/Kinshasa" => "Kinshasa",
                "Africa/Lagos" => "Lagos",
                "Africa/Libreville" => "Libreville",
                "Africa/Lome" => "Lome",
                "Africa/Luanda" => "Luanda",
                "Africa/Lubumbashi" => "Lubumbashi",
                "Africa/Lusaka" => "Lusaka",
                "Africa/Malabo" => "Malabo",
                "Africa/Maputo" => "Maputo",
                "Africa/Maseru" => "Maseru",
                "Africa/Mbabane" => "Mbabane",
                "Africa/Mogadishu" => "Mogadishu",
                "Africa/Monrovia" => "Monrovia",
                "Africa/Nairobi" => "Nairobi",
                "Africa/Ndjamena" => "Ndjamena",
                "Africa/Niamey" => "Niamey",
                "Africa/Nouakchott" => "Nouakchott",
                "Africa/Ouagadougou" => "Ouagadougou",
                "Africa/Porto-Novo" => "Porto-Novo",
                "Africa/Sao_Tome" => "Sao Tome",
                "Africa/Timbuktu" => "Timbuktu",
                "Africa/Tripoli" => "Tripoli",
                "Africa/Tunis" => "Tunis",
                "Africa/Windhoek" => "Windhoek",
            ],
            
            "Australia" => [
                "Australia/ACT" => "ACT",
                "Australia/Adelaide" => "Adelaide",
                "Australia/Brisbane" => "Brisbane",
                "Australia/Broken_Hill" => "Broken Hill",
                "Australia/Canberra" => "Canberra",
                "Australia/Currie" => "Currie",
                "Australia/Darwin" => "Darwin",
                "Australia/Eucla" => "Eucla",
                "Australia/Hobart" => "Hobart",
                "Australia/LHI" => "LHI",
                "Australia/Lindeman" => "Lindeman",
                "Australia/Lord_Howe" => "Lord Howe",
                "Australia/Melbourne" => "Melbourne",
                "Australia/North" => "North",
                "Australia/NSW" => "NSW",
                "Australia/Perth" => "Perth",
                "Australia/Queensland" => "Queensland",
                "Australia/South" => "South",
                "Australia/Sydney" => "Sydney",
                "Australia/Tasmania" => "Tasmania",
                "Australia/Victoria" => "Victoria",
                "Australia/West" => "West",
                "Australia/Yancowinna" => "Yancowinna",
            ],

            "Indian" => [
                "Indian/Antananarivo" => "Antananarivo",
                "Indian/Chagos" => "Chagos",
                "Indian/Christmas" => "Christmas",
                "Indian/Cocos" => "Cocos",
                "Indian/Comoro" => "Comoro",
                "Indian/Kerguelen" => "Kerguelen",
                "Indian/Mahe" => "Mahe",
                "Indian/Maldives" => "Maldives",
                "Indian/Mauritius" => "Mauritius",
                "Indian/Mayotte" => "Mayotte",
                "Indian/Reunion" => "Reunion",
            ],
            
            "Atlantic" => [
                "Atlantic/Azores" => "Azores",
                "Atlantic/Bermuda" => "Bermuda",
                "Atlantic/Canary" => "Canary",
                "Atlantic/Cape_Verde" => "Cape Verde",
                "Atlantic/Faeroe" => "Faeroe",
                "Atlantic/Faroe" => "Faroe",
                "Atlantic/Jan_Mayen" => "Jan Mayen",
                "Atlantic/Madeira" => "Madeira",
                "Atlantic/Reykjavik" => "Reykjavik",
                "Atlantic/South_Georgia" => "South Georgia",
                "Atlantic/Stanley" => "Stanley",
                "Atlantic/St_Helena" => "St Helena",
            ],

            "Pacific" => [
                "Pacific/Apia" => "Apia",
                "Pacific/Auckland" => "Auckland",
                "Pacific/Chatham" => "Chatham",
                "Pacific/Easter" => "Easter",
                "Pacific/Efate" => "Efate",
                "Pacific/Enderbury" => "Enderbury",
                "Pacific/Fakaofo" => "Fakaofo",
                "Pacific/Fiji" => "Fiji",
                "Pacific/Funafuti" => "Funafuti",
                "Pacific/Galapagos" => "Galapagos",
                "Pacific/Gambier" => "Gambier",
                "Pacific/Guadalcanal" => "Guadalcanal",
                "Pacific/Guam" => "Guam",
                "Pacific/Honolulu" => "Honolulu",
                "Pacific/Johnston" => "Johnston",
                "Pacific/Kiritimati" => "Kiritimati",
                "Pacific/Kosrae" => "Kosrae",
                "Pacific/Kwajalein" => "Kwajalein",
                "Pacific/Majuro" => "Majuro",
                "Pacific/Marquesas" => "Marquesas",
                "Pacific/Midway" => "Midway",
                "Pacific/Nauru" => "Nauru",
                "Pacific/Niue" => "Niue",
                "Pacific/Norfolk" => "Norfolk",
                "Pacific/Noumea" => "Noumea",
                "Pacific/Pago_Pago" => "Pago Pago",
                "Pacific/Palau" => "Palau",
                "Pacific/Pitcairn" => "Pitcairn",
                "Pacific/Ponape" => "Ponape",
                "Pacific/Port_Moresby" => "Port Moresby",
                "Pacific/Rarotonga" => "Rarotonga",
                "Pacific/Saipan" => "Saipan",
                "Pacific/Samoa" => "Samoa",
                "Pacific/Tahiti" => "Tahiti",
                "Pacific/Tarawa" => "Tarawa",
                "Pacific/Tongatapu" => "Tongatapu",
                "Pacific/Truk" => "Truk",
                "Pacific/Wake" => "Wake",
                "Pacific/Wallis" => "Wallis",
                "Pacific/Yap" => "Yap",
            ],
            
            "Antarctica" => [
                "Antarctica/Casey" => "Casey",
                "Antarctica/Davis" => "Davis",
                "Antarctica/DumontDUrville" => "DumontDUrville",
                "Antarctica/Macquarie" => "Macquarie",
                "Antarctica/Mawson" => "Mawson",
                "Antarctica/McMurdo" => "McMurdo",
                "Antarctica/Palmer" => "Palmer",
                "Antarctica/Rothera" => "Rothera",
                "Antarctica/South_Pole" => "South Pole",
                "Antarctica/Syowa" => "Syowa",
                "Antarctica/Vostok" => "Vostok",
            ],

            "Arctic" => [
                "Arctic/Longyearbyen" => "Longyearbyen",
            ]
        ];
    }

}
