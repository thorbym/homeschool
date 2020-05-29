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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$request->validate([
            SOMETHING IN HERE
        ]);*/

        $event = new Event([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'link' => $request->get('link'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'days_of_week' => json_encode($request->get('days_of_week')),
            'category_id' => $request->get('category_id'),
            'requires_supervision' => $request->get('requires_supervision') ? 1 : 0,
            'dfe_approved' => $request->get('dfe_approved') ? 1 : 0,
            'catchup_link' => $request->get('catchup_link') ? $request->get('catchup_link') : null,
            'minimum_age' => $request->get('minimum_age'),
            'maximum_age' => $request->get('maximum_age')
        ]);
        $event->save();

        return redirect('calendar');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Axios route to get one record by ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {

        $categories = Category::get();

        if ($id > 0) {
            $query = DB::table('events')
                ->join('categories', 'categories.id', '=', 'events.category_id');
            if (Auth::check()) {
                $query->leftJoin('favourites', function($join) {
                    $join->on('favourites.event_id', '=', 'events.id');
                    $join->where('favourites.user_id', '=', Auth::user()->id);
                });
            }
            $query->select(
                'events.id',
                'events.title',
                'events.description',
                'events.link',
                'events.start_time',
                'events.end_time',
                'events.days_of_week',
                'events.requires_supervision',
                'events.dfe_approved',
                'events.catchup_link',
                'events.minimum_age',
                'events.maximum_age',
                'events.category_id',
                'categories.category',
                'categories.colour',
                Auth::check() ? DB::raw('(case when favourites.id is null then 0 else favourites.id end) as favourite_id') : DB::raw('0 AS favourite_id')
            );
            $query->where('events.id', '=', $id);
            $event = $query->first();

            if (Auth::check() && Auth::user()->isAdmin()) {
                $view = view('modals.eventUpdate', compact('categories', 'event'))->render();
            } else {
                $view = view('modals.eventView', compact('categories', 'event'))->render();
            }

        } else {

            $view = view('modals.eventNew', compact('categories'))->render();

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
        //
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
        $event = Event::find($id);

        $event->title = $request->get('title');
        $event->description = $request->get('description');
        $event->link = $request->get('link');
        $event->start_time = $request->get('start_time');
        $event->end_time = $request->get('end_time');
        $event->days_of_week = json_encode($request->get('days_of_week'));
        $event->category_id = $request->get('category_id');
        $event->requires_supervision = $request->get('requires_supervision') ? 1 : 0;
        $event->dfe_approved = $request->get('dfe_approved') ? 1 : 0;
        $event->catchup_link = $request->get('catchup_link');
        $event->minimum_age = $request->get('minimum_age');
        $event->maximum_age = $request->get('maximum_age');

        $event->save();

        return redirect('calendar');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
