<?php

namespace App\Http\Controllers;

use \DB;
use App\Event;
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
            'category_id' => $request->get('category_id')
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
        $event = DB::table('events')
            ->join('categories', 'categories.id', '=', 'events.category_id')
            ->select('events.id', 'events.title', 'events.description', 'events.link', 'events.start_time AS startTime', 'events.end_time AS endTime', 'events.days_of_week AS daysOfWeek', 'categories.category', 'categories.colour')
            ->where('events.id', '=', $id)
            ->first();

        return response()->json($event);
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
        //
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
