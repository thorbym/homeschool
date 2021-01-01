<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('calendar');
});*/

Auth::routes();
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacyPolicy', 'privacyPolicy')->name('privacyPolicy');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/calendar', 'HomeController@showCalendar')->name('calendar');
Route::get('/calendar/quickStart', 'HomeController@showCalendarWithQuickStart')->name('calendarQuickStart');
Route::get('/list/quickStart', 'HomeController@showListWithQuickStart')->name('listQuickStart');
Route::get('/list', 'HomeController@showList')->name('list');

// EVENTS
Route::post('/event', 'EventController@store')->name('storeEvent');
Route::patch('/event/{id}', 'EventController@update')->name('updateEvent');
Route::delete('/event/{id}', 'EventController@destroy')->name('destroyEvent');

Route::get('/event/{id}/showFromList', 'EventController@showEventFromList');
Route::get('/event/{id}/showFromCalendar', 'EventController@showEventFromCalendar');

// EVENTS API
Route::get('/event/create', 'EventController@create')->name('createEvent');
Route::get('/event/{id}/edit', 'EventController@edit')->name('editEvent');
Route::get('/api/event/{eventCalendarId}/editFromCalendar', 'EventController@editEventFromCalendar');
Route::get('/api/event/{id}/editFromList', 'EventController@editEventFromList');
Route::get('/api/event/{eventCalendarId}/showFromCalendar', 'EventController@showEventFromCalendar');
Route::get('/api/event/{id}/showFromList', 'EventController@showEventFromList');
Route::get('/api/events/calendar/{filters}', 'EventController@getCalendarEvents');
Route::get('/api/events/list/{filters}', 'EventController@getListEvents');

// REVIEWS
Route::post('/review/{event_id}', 'EventController@storeReview')->name('storeReview');

// CATEGORIES
Route::get('/categories', 'HomeController@showCategories')->name('categories');
Route::post('/category', 'CategoryController@store')->name('storeCategory');
Route::patch('/category/{id}', 'CategoryController@update')->name('updateCategory');
Route::delete('/category/{id}', 'CategoryController@destroy')->name('destroyCategory');
// CATEGORIES API
Route::get('/api/category/create', 'CategoryController@create');
Route::get('/api/category/{id}/edit', 'CategoryController@edit');

// FAVOURITES API
Route::post('/api/favourite', 'FavouriteController@store');
Route::delete('/api/favourite/{event_id}', 'FavouriteController@destroy');

// QUICKSTART API
Route::get('/api/quickStart/show', 'HomeController@showQuickStart');

// LOGIN WARNING API
Route::get('/api/loginWarning/show/{message}/{returnUrl}', 'HomeController@showLoginWarning');

// CLICKTHROUGHS API
Route::post('/api/clickthrough', 'ClickThroughController@store');