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
Route::get('lab/{lab}', 'LabController@show')->name('lab.show');
Route::get('lab/{lab}/members', 'LabMemberController@edit')->name('lab.members.edit');
Route::post('lab/{lab}/members', 'LabMemberController@update')->name('lab.members.update');

Route::get('options', 'OptionsController@edit')->name('options.edit');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
