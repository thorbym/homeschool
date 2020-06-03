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

Auth::routes(['reset' => false]);
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacyPolicy', 'privacyPolicy')->name('privacyPolicy');
Route::get('protected', ['middleware' => ['auth', 'admin'], function() {
    Route::get('/categories', 'HomeController@listCategories')->name('categories');
    Route::post('/category', 'CategoryController@store')->name('category');
}]);
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/calendar', 'HomeController@showCalendar')->name('calendar');
Route::get('/calendar/quickStart', 'HomeController@showCalendarWithQuickStart')->name('calendarQuickStart');
Route::get('/list', 'HomeController@showList')->name('list');
Route::post('/event', 'EventController@store')->name('storeEvent');
Route::post('/event/{id}', 'EventController@update')->name('updateEvent');

Route::get('/api/event/{id}', 'EventController@get');
Route::post('/api/favourite', 'FavouriteController@store');
Route::delete('/api/favourite/{id}', 'FavouriteController@destroy');
