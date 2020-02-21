<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('api/hello/{ip?}', 'Api\MachineLogController@store')->name('api.hello');
Route::get('api/goodbye/{ip?}', 'Api\MachineLogController@destroy')->name('api.goodbye');

Route::post('api/lab', 'Api\LabController@store')->name('api.lab.store');
Route::delete('api/lab/{name}', 'Api\LabController@destroy')->name('api.lab.destroy');

Route::post('api/labmachines/{name}', 'Api\LabMachineController@update')->name('api.lab.machines.update');
