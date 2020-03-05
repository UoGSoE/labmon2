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

Route::get('api/hello/{ip?}', 'Api\MachineController@store')->name('api.hello');
Route::get('api/goodbye/{ip?}', 'Api\MachineController@destroy')->name('api.goodbye');

Route::post('api/bzzz/{ip?}', 'Api\MachineController@update')->name('api.machine.update');

Route::post('api/lab', 'Api\LabController@store')->name('api.lab.store');
Route::delete('api/lab/{name}', 'Api\LabController@destroy')->name('api.lab.destroy');

Route::get('api/labstats/busy/{name}', 'Api\LabBusyController@show')->name('api.lab.busy');
Route::get('api/labstats/dates', 'Api\LabStatDateController@index')->name('api.labstats.dates');

Route::get('api/labs/graphable', 'Api\GraphableLabController@index')->name('api.lab.graphable');

Route::get('api/rdp/available/{name}', 'Api\RdpLabController@show')->name('api.lab.rdp_machines');
Route::get('api/rdp/labsavailable', 'Api\RdpLabController@index')->name('api.lab.rdp_labs');

Route::post('api/labmachines/{name}', 'Api\LabMachineController@update')->name('api.lab.machines.update');

Route::get('api/lastseen/{ip}', 'Api\LastSeenController@show')->name('api.lastseen');
