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
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect('list');
        } else {
            return view('home');
        }
    }

    public function showCategories()
    {
        $categories = Category::get();
        return view('categories', compact('categories'));
    }

    public function showCalendar()
    {
        $categories = Category::get();
        $data = [
            'categories' => $categories
        ];
        return view('calendar', compact('data'));
    }

    public function showCalendarWithQuickStart()
    {
        $categories = Category::get();
        $data = [
            'categories' => $categories,
            'quickStart' => true
        ];
        return view('calendar', compact('data'));
    }

    public function showListWithQuickStart()
    {
        $categories = Category::get();
        $data = [
            'categories' => $categories,
            'quickStart' => true
        ];
        return view('list', compact('data'));
    }

    public function showQuickStart()
    {
        $categories = Category::get();
        
        $view = view('modals.quickStart', compact('categories'))->render();

        return response()->json($view);
    }

    public function showLoginWarning($message, $returnUrl)
    {        
        if ($message == "addRating") {
            $viewMessage = "To rate or review events";
        }
        if ($message == "addFavourite") {
            $viewMessage = "To save this event to your favourites";
        }
        if ($message == "filterFavourites") {
            $viewMessage = "To filter your favourite events";
        }
        session()->put('url.intended', base64_decode($returnUrl));
        $view = view('modals.loginWarning', compact('viewMessage'))->render();

        return response()->json($view);
    }

    public function showList()
    {
        $categories = Category::get();
        $data = [
            'categories' => $categories
        ];
        return view('list', compact('data'));
    }

}
