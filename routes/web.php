<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/shout', 'HomeController@shoutHome')->name('shout');
Route::get('/shout/{nickname}', 'HomeController@shoutPublic')->name('shout.public');
Route::post('/savestatus', 'HomeController@saveStatus')->name('shout.save');
Route::get('/profile', 'HomeController@profile')->name('shout.profile');
Route::post('/saveprofile', 'HomeController@saveProfile')->name('shout.saveprofile');
Route::get('/shout/makefriend/{friendId}', 'HomeController@makeFriend')->name('shoute.makefriend');
Route::get('/shout/unfriend/{friendId}', 'HomeController@unFriend')->name('shoute.unfriend');
