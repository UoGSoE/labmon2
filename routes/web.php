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
Auth::routes();
Route::group(['middleware' => ['auth', 'allowed']], function () {
    Route::get('/', 'LabController@index')->name('lab.index');
    Route::get('lab/{lab}', 'LabController@show')->name('lab.show');
    Route::get('lab/{lab}/members', 'LabMemberController@edit')->name('lab.members.edit');
    Route::post('lab/{lab}/members', 'LabMemberController@update')->name('lab.members.update');

    Route::get('machine', 'MachineController@index')->name('machine.index');

    Route::get('options', 'OptionsController@edit')->name('options.edit');
    Route::post('options', 'OptionsController@update')->name('options.update');

    Route::redirect('/home', '/')->name('home');
});
