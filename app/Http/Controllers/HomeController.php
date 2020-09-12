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

    public function showLoginWarning()
    {        
        $view = view('modals.loginWarning')->render();

        return response()->json($view);
    }

    public function showAddEventWarning()
    {        
        $view = view('modals.addEventWarning')->render();

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

    public function showUnapprovedList()
    {
        $categories = Category::get();
        $data = [
            'categories' => $categories
        ];
        return view('unapprovedList', compact('data'));
    }

    public function billingPortal(Request $request)
    {
        return $request->user()->redirectToBillingPortal();
    }

    public function checkout(Request $request)
    {
        return view('payments.checkout', [
            'intent' => $request->user()->createSetupIntent()
        ]);
    }

    public function pricing()
    {
        return view('pricing');
    }

    public function loginOrRegister($package)
    {
        $view = view('modals.loginOrRegister', compact('package'))->render();

        return response()->json($view);
    }

    public function fastStore(Request $request){

        $user = $request->user();
        $user->createOrGetStripeCustomer();
        $user->addPaymentMethod($request->get('paymentMethod'));
        $user->newSubscription('Contributor', 'price_1HHFHqDW1slEgvER8r37ZSPD')
            ->create($request->get('paymentMethod'));

        return true;
    }

}
