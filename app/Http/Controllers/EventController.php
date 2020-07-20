<?php

namespace App\Http\Controllers;

use Auth;
use \DB;
use App\Category;
use App\Event;
use App\EventCalendar;
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

        $timezones = self::getTimeZones();

        $view = view('modals.eventCrud', compact('categories', 'event', 'timezones'))->render();

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
            'timezone' => 'present|string|nullable',
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
            'free_content' => $request->get('free_content') ?   1 : 0,
            'timezone' => $request->get('timezone') ? $request->get('timezone') : null,
        ]);
        $event->save();

        // if the event has a start time, then we need to add an event_calendar for it too
        if ($request->get('start_time')) {
            self::createEventCalendar($event->id, $request->get('days_of_week'), $request->get('start_time'), $request->get('end_time'), $request->get('timezone'));
        }

        return redirect()->back();
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
        $event = $eventCalendar->event;
        $fromCalendar = true;
        $view = view('modals.eventDetails', compact('categories', 'event', 'eventCalendar', 'fromCalendar'))->render();

        return response()->json($view);
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
        if ($event->start_time) {
            $eventCalendar = EventCalendar::where('event_id', $id)
                ->where('end_utc', '>', gmdate('Y-m-d H:i'))
                ->orderBy('end_utc', 'ASC')
                ->first();
        }
        $fromCalendar = false;
        $view = view('modals.eventDetails', compact('categories', 'event', 'eventCalendar', 'fromCalendar'))->render();

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
            DB::raw('(case when events.free_content = 0 then CONCAT(events.title, " [PAID]") else events.title end) as title'),
            'events.description',
            DB::raw('CONCAT(REPLACE(event_calendars.start," ","T"), utc_offset) as start'),
            DB::raw('CONCAT(REPLACE(event_calendars.end," ","T"), utc_offset) as end'),
            'events.minimum_age',
            'events.maximum_age',
            'events.dfe_approved',
            'events.requires_supervision',
            'events.free_content',
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
            $timezones = self::getTimeZones();
            $view = view('modals.eventCrud', compact('categories', 'event', 'timezones'))->render();

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
            $timezones = self::getTimeZones();
            $view = view('modals.eventCrud', compact('categories', 'event', 'timezones'))->render();

        } else {
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
            'timezone' => 'present|string|nullable',
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
        $event->timezone = $request->get('timezone') ? $request->get('timezone') : null;

        $event->save();

        // if the event has a start time, then we may well need to update event_calendars
        if ($request->get('start_time')) {
            // clear out what's there
            EventCalendar::where('event_id', $id)->delete();
            // update it with new times
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

    public static function getTimeZones () {
        return [
            'Africa' => [
                'Africa/Algiers' => 'Algeria (+01:00)',
                'Africa/Gaborone' => 'Botswana (+02:00)',
                'Africa/Douala' => 'Cameroon (+01:00)',
                'Africa/Bangui' => 'Central African Republic (+01:00)',
                'Africa/Ndjamena' => 'Chad (+01:00)',
                'Africa/Kinshasa' => 'Democratic Republic of the Congo (+01:00)',
                'Africa/Djibouti' => 'Djibouti (+03:00)',
                'Africa/Cairo' => 'Egypt (+02:00)',
                'Africa/Malabo' => 'Equatorial Guinea (+01:00)',
                'Africa/Asmara' => 'Eritrea (+03:00)',
                'Africa/Addis_Ababa' => 'Ethiopia (+03:00)',
                'Africa/Libreville' => 'Gabon (+01:00)',
                'Africa/Banjul' => 'Gambia (+00:00)',
                'Africa/Accra' => 'Ghana (+00:00)',
                'Africa/Conakry' => 'Guinea (+00:00)',
                'Africa/Bissau' => 'Guinea-Bissau (+00:00)',
                'Africa/Abidjan' => 'Ivory Coast (+00:00)',
                'Africa/Nairobi' => 'Kenya (+03:00)',
                'Africa/Maseru' => 'Lesotho (+02:00)',
                'Africa/Monrovia' => 'Liberia (+00:00)',
                'Africa/Tripoli' => 'Libya (+02:00)',
                'Africa/Blantyre' => 'Malawi (+02:00)',
                'Africa/Bamako' => 'Mali (+00:00)',
                'Africa/Nouakchott' => 'Mauritania (+00:00)',
                'Africa/Casablanca' => 'Morocco (+01:00)',
                'Africa/Maputo' => 'Mozambique (+02:00)',
                'Africa/Windhoek' => 'Namibia (+01:00)',
                'Africa/Niamey' => 'Niger (+01:00)',
                'Africa/Lagos' => 'Nigeria (+01:00)',
                'Africa/Brazzaville' => 'Republic of the Congo (+01:00)',
                'Africa/Kigali' => 'Rwanda (+02:00)',
                'Africa/Sao_Tome' => 'Sao Tome and Principe (+00:00)',
                'Africa/Dakar' => 'Senegal (+00:00)',
                'Africa/Freetown' => 'Sierra Leone (+00:00)',
                'Africa/Mogadishu' => 'Somalia (+03:00)',
                'Africa/Johannesburg' => 'South Africa (+02:00)',
                'Africa/Juba' => 'South Sudan (+03:00)',
                'Africa/Khartoum' => 'Sudan (+03:00)',
                'Africa/Mbabane' => 'Swaziland (+02:00)',
                'Africa/Dar_es_Salaam' => 'Tanzania (+03:00)',
                'Africa/Lome' => 'Togo (+00:00)',
                'Africa/Tunis' => 'Tunisia (+01:00)',
                'Africa/Kampala' => 'Uganda (+03:00)',
                'Africa/El_Aaiun' => 'Western Sahara (+00:00)',
                'Africa/Lusaka' => 'Zambia (+02:00)',
                'Africa/Harare' => 'Zimbabwe (+02:00)',
            ],

            'America' => [
                'America/Nassau' => 'Bahamas (-04:00)',
                'America/Belize' => 'Belize (-06:00)',
                'America/Noronha' => 'Brazil (-02:00)',
                'America/Tortola' => 'British Virgin Islands (-04:00)',
                'America/St_Johns' => 'Canada (-02:30)',
                'America/Cayman' => 'Cayman Islands (-05:00)',
                'America/Santiago' => 'Chile (-04:00)',
                'America/Bogota' => 'Colombia (-05:00)',
                'America/Costa_Rica' => 'Costa Rica (-06:00)',
                'America/Havana' => 'Cuba (-04:00)',
                'America/Curacao' => 'Curaçao (-04:00)',
                'America/Dominica' => 'Dominica (-04:00)',
                'America/Santo_Domingo' => 'Dominican Republic (-04:00)',
                'America/Guayaquil' => 'Ecuador (-05:00)',
                'America/El_Salvador' => 'El Salvador (-06:00)',
                'America/Cayenne' => 'French Guiana (-03:00)',
                'America/Godthab' => 'Greenland (-02:00)',
                'America/Grenada' => 'Grenada (-04:00)',
                'America/Guadeloupe' => 'Guadeloupe (-04:00)',
                'America/Guatemala' => 'Guatemala (-06:00)',
                'America/Guyana' => 'Guyana (-04:00)',
                'America/Port-au-Prince' => 'Haiti (-05:00)',
                'America/Tegucigalpa' => 'Honduras (-06:00)',
                'America/Jamaica' => 'Jamaica (-05:00)',
                'America/Martinique' => 'Martinique (-04:00)',
                'America/Mexico_City' => 'Mexico (-05:00)',
                'America/Montserrat' => 'Montserrat (-04:00)',
                'America/Managua' => 'Nicaragua (-06:00)',
                'America/Panama' => 'Panama (-05:00)',
                'America/Asuncion' => 'Paraguay (-04:00)',
                'America/Lima' => 'Peru (-05:00)',
                'America/Puerto_Rico' => 'Puerto Rico (-04:00)',
                'America/St_Kitts' => 'Saint Kitts and Nevis (-04:00)',
                'America/St_Lucia' => 'Saint Lucia (-04:00)',
                'America/Marigot' => 'Saint Martin (-04:00)',
                'America/Miquelon' => 'Saint Pierre and Miquelon (-02:00)',
                'America/St_Vincent' => 'Saint Vincent and the Grenadines (-04:00)',
                'America/Lower_Princes' => 'Sint Maarten (-04:00)',
                'America/Paramaribo' => 'Suriname (-03:00)',
                'America/Port_of_Spain' => 'Trinidad and Tobago (-04:00)',
                'America/Grand_Turk' => 'Turks and Caicos Islands (-04:00)',
                'America/St_Thomas' => 'U.S. Virgin Islands (-04:00)',
                'America/New_York' => 'United States (-04:00)',
                'America/Montevideo' => 'Uruguay (-03:00)',
                'Europe/Vatican' => 'Vatican (+02:00)',
                'America/Caracas' => 'Venezuela (-04:30)',
            ],

            'Arctic' => [
                'Arctic/Longyearbyen' => 'Svalbard and Jan Mayen (+02:00)',
            ],

            'Asia' => [
                'Asia/Thimphu' => 'Bhutan (+06:00)',
                'Asia/Phnom_Penh' => 'Cambodia (+07:00)',
                'Asia/Shanghai' => 'China (+08:00)',
                'Asia/Nicosia' => 'Cyprus (+03:00)',
                'Asia/Dili' => 'East Timor (+09:00)',
                'Asia/Tbilisi' => 'Georgia (+04:00)',
                'Asia/Hong_Kong' => 'Hong Kong (+08:00)',
                'Asia/Kolkata' => 'India (+05:30)',
                'Asia/Jakarta' => 'Indonesia (+07:00)',
                'Asia/Tehran' => 'Iran (+04:30)',
                'Asia/Baghdad' => 'Iraq (+03:00)',
                'Asia/Jerusalem' => 'Israel (+03:00)',
                'Asia/Tokyo' => 'Japan (+09:00)',
                'Asia/Amman' => 'Jordan (+03:00)',
                'Asia/Almaty' => 'Kazakhstan (+06:00)',
                'Asia/Kuwait' => 'Kuwait (+03:00)',
                'Asia/Bishkek' => 'Kyrgyzstan (+06:00)',
                'Asia/Vientiane' => 'Laos (+07:00)',
                'Asia/Beirut' => 'Lebanon (+03:00)',
                'Asia/Macau' => 'Macao (+08:00)',
                'Asia/Kuala_Lumpur' => 'Malaysia (+08:00)',
                'Asia/Ulaanbaatar' => 'Mongolia (+08:00)',
                'Asia/Rangoon' => 'Myanmar (+06:30)',
                'Asia/Kathmandu' => 'Nepal (+05:45)',
                'Asia/Pyongyang' => 'North Korea (+09:00)',
                'Asia/Muscat' => 'Oman (+04:00)',
                'Asia/Karachi' => 'Pakistan (+05:00)',
                'Asia/Gaza' => 'Palestinian Territory (+02:00)',
                'Asia/Manila' => 'Philippines (+08:00)',
                'Asia/Qatar' => 'Qatar (+03:00)',
                'Asia/Riyadh' => 'Saudi Arabia (+03:00)',
                'Asia/Singapore' => 'Singapore (+08:00)',
                'Asia/Seoul' => 'South Korea (+09:00)',
                'Asia/Colombo' => 'Sri Lanka (+05:30)',
                'Asia/Damascus' => 'Syria (+03:00)',
                'Asia/Taipei' => 'Taiwan (+08:00)',
                'Asia/Dushanbe' => 'Tajikistan (+05:00)',
                'Asia/Bangkok' => 'Thailand (+07:00)',
                'Asia/Ashgabat' => 'Turkmenistan (+05:00)',
                'Asia/Samarkand' => 'Uzbekistan (+05:00)',
                'Asia/Ho_Chi_Minh' => 'Vietnam (+07:00)',
                'Asia/Aden' => 'Yemen (+03:00)',
            ],

            'Atlantic' => [
                'Atlantic/Cape_Verde' => 'Cape Verde (-01:00)',
                'Atlantic/Stanley' => 'Falkland Islands (-03:00)',
                'Atlantic/Faroe' => 'Faroe Islands (+01:00)',
                'Atlantic/Reykjavik' => 'Iceland (+00:00)',
                'Atlantic/St_Helena' => 'Saint Helena (+00:00)',
                'Atlantic/South_Georgia' => 'South Georgia and the South Sandwich Islands (-02:00)',
            ],

            'Europe' => [
                'Europe/Minsk' => 'Belarus (+03:00)',
                'Europe/Zagreb' => 'Croatia (+02:00)',
                'Europe/Prague' => 'Czech Republic (+02:00)',
                'Europe/Copenhagen' => 'Denmark (+02:00)',
                'Europe/Tallinn' => 'Estonia (+03:00)',
                'Europe/Helsinki' => 'Finland (+03:00)',
                'Europe/Paris' => 'France (+02:00)',
                'Europe/Berlin' => 'Germany (+02:00)',
                'Europe/Gibraltar' => 'Gibraltar (+02:00)',
                'Europe/Athens' => 'Greece (+03:00)',
                'Europe/Guernsey' => 'Guernsey (+01:00)',
                'Europe/Budapest' => 'Hungary (+02:00)',
                'Europe/Dublin' => 'Ireland (+01:00)',
                'Europe/Isle_of_Man' => 'Isle of Man (+01:00)',
                'Europe/Rome' => 'Italy (+02:00)',
                'Europe/Jersey' => 'Jersey (+01:00)',
                'Europe/Riga' => 'Latvia (+03:00)',
                'Europe/Vaduz' => 'Liechtenstein (+02:00)',
                'Europe/Vilnius' => 'Lithuania (+03:00)',
                'Europe/Luxembourg' => 'Luxembourg (+02:00)',
                'Europe/Skopje' => 'Macedonia (+02:00)',
                'Europe/Malta' => 'Malta (+02:00)',
                'Europe/Chisinau' => 'Moldova (+03:00)',
                'Europe/Monaco' => 'Monaco (+02:00)',
                'Europe/Podgorica' => 'Montenegro (+02:00)',
                'Europe/Amsterdam' => 'Netherlands (+02:00)',
                'Europe/Oslo' => 'Norway (+02:00)',
                'Europe/Warsaw' => 'Poland (+02:00)',
                'Europe/Lisbon' => 'Portugal (+01:00)',
                'Europe/Bucharest' => 'Romania (+03:00)',
                'Europe/Kaliningrad' => 'Russia (+03:00)',
                'Europe/San_Marino' => 'San Marino (+02:00)',
                'Europe/Belgrade' => 'Serbia (+02:00)',
                'Europe/Bratislava' => 'Slovakia (+02:00)',
                'Europe/Ljubljana' => 'Slovenia (+02:00)',
                'Europe/Madrid' => 'Spain (+02:00)',
                'Europe/Stockholm' => 'Sweden (+02:00)',
                'Europe/Zurich' => 'Switzerland (+02:00)',
                'Europe/Istanbul' => 'Turkey (+03:00)',
                'Europe/Kiev' => 'Ukraine (+03:00)',
                'Europe/London' => 'United Kingdom (+01:00)',
            ],

            'Indian' => [
                'Indian/Chagos' => 'British Indian Ocean Territory (+06:00)',
                'Indian/Christmas' => 'Christmas Island (+07:00)',
                'Indian/Cocos' => 'Cocos Islands (+06:30)',
                'Indian/Comoro' => 'Comoros (+03:00)',
                'Indian/Kerguelen' => 'French Southern Territories (+05:00)',
                'Indian/Antananarivo' => 'Madagascar (+03:00)',
                'Indian/Maldives' => 'Maldives (+05:00)',
                'Indian/Mauritius' => 'Mauritius (+04:00)',
                'Indian/Mayotte' => 'Mayotte (+03:00)',
                'Indian/Reunion' => 'Reunion (+04:00)',
                'Indian/Mahe' => 'Seychelles (+04:00)',
            ],

            'Pacific' => [
                'Pacific/Rarotonga' => 'Cook Islands (-10:00)',
                'Pacific/Fiji' => 'Fiji (+12:00)',
                'Pacific/Tahiti' => 'French Polynesia (-10:00)',
                'Pacific/Guam' => 'Guam (+10:00)',
                'Pacific/Tarawa' => 'Kiribati (+12:00)',
                'Pacific/Majuro' => 'Marshall Islands (+12:00)',
                'Pacific/Chuuk' => 'Micronesia (+10:00)',
                'Pacific/Nauru' => 'Nauru (+12:00)',
                'Pacific/Noumea' => 'New Caledonia (+11:00)',
                'Pacific/Auckland' => 'New Zealand (+12:00)',
                'Pacific/Niue' => 'Niue (-11:00)',
                'Pacific/Norfolk' => 'Norfolk Island (+11:30)',
                'Pacific/Saipan' => 'Northern Mariana Islands (+10:00)',
                'Pacific/Palau' => 'Palau (+09:00)',
                'Pacific/Port_Moresby' => 'Papua New Guinea (+10:00)',
                'Pacific/Pitcairn' => 'Pitcairn (-08:00)',
                'Pacific/Apia' => 'Samoa (+13:00)',
                'Pacific/Guadalcanal' => 'Solomon Islands (+11:00)',
                'Pacific/Fakaofo' => 'Tokelau (+14:00)',
                'Pacific/Tongatapu' => 'Tonga (+13:00)',
                'Pacific/Funafuti' => 'Tuvalu (+12:00)',
                'Pacific/Johnston' => 'United States Minor Outlying Islands (-10:00)',
                'Pacific/Efate' => 'Vanuatu (+11:00)',
                'Pacific/Wallis' => 'Wallis and Futuna (+12:00)',
            ],

            'Other' => [
                'UTC' => 'UTC',
            ],
        ];
    }
}
