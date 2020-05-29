<?php

namespace App\Http\Controllers;

use Auth;
use App\Category;
use App\Event;
use App\Favourite;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function listCategories()
    {
        $categories = Category::get();
        return view('categories', compact('categories'));
    }

    public function showCalendar()
    {
        $categories = Category::get();
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
            'events.start_time AS startTime',
            'events.end_time AS endTime',
            'events.days_of_week AS daysOfWeek',
            'events.minimum_age',
            'events.maximum_age',
            'events.dfe_approved',
            'events.requires_supervision',
            'categories.category',
            'categories.colour',
            Auth::check() ? DB::raw('(case when favourites.id is null then 0 else favourites.id end) as favourite_id') : DB::raw('0 AS favourite_id')
        );
        $events = $query->get();
        $data = [
            'categories' => $categories,
            'events' => $events
        ];
        return view('calendar', compact('data'));
    }

    public function showList()
    {
        $categories = Category::get();
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
            'events.start_time AS startTime',
            'events.end_time AS endTime',
            'events.days_of_week AS daysOfWeek',
            'events.minimum_age',
            'events.maximum_age',
            'events.dfe_approved',
            'events.requires_supervision',
            'categories.category',
            'categories.colour',
            Auth::check() ? DB::raw('(case when favourites.id is null then 0 else favourites.id end) as favourite_id') : DB::raw('0 AS favourite_id')
        );
        $events = $query->get();
        $data = [
            'categories' => $categories,
            'events' => $events
        ];
        return view('list', compact('data'));
    }

}
