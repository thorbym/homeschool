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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('protected', ['middleware' => ['auth', 'admin'], function() {
    return "this page requires that you be logged in and an Admin";
}]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/calendar', 'HomeController@showCalendar')->name('calendar');
Route::get('/categories', 'HomeController@listCategories')->name('categories');
Route::post('/event', 'EventController@store')->name('event');
Route::get('/event/{id}', 'EventController@get');
Route::post('/category', 'CategoryController@store')->name('category');