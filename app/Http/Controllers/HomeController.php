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
        return view('home');
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

    public function showQuickStart()
    {
        $categories = Category::get();
        
        $view = view('modals.quickStart', compact('categories'))->render();

        return response()->json($view);
    }

    public function showLoginWarning()
    {        
        $view = view('modals.loginWarning')->render();

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
