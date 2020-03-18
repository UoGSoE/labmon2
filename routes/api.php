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

Route::get('hello/{ip?}', 'Api\MachineController@store')->name('api.hello');
Route::get('goodbye/{ip?}', 'Api\MachineController@destroy')->name('api.goodbye');

Route::post('bzzz/{ip?}', 'Api\MachineController@update')->name('api.machine.update');

Route::post('lab', 'Api\LabController@store')->name('api.lab.store');
Route::delete('lab/{name}', 'Api\LabController@destroy')->name('api.lab.destroy');

Route::get('labstats/busy/{name}', 'Api\LabBusyController@show')->name('api.lab.busy');
Route::get('labstats/dates', 'Api\LabStatDateController@index')->name('api.labstats.dates');

Route::get('labs/graphable', 'Api\GraphableLabController@index')->name('api.lab.graphable');

Route::get('labs/graphstats', 'Api\LabGraphStatsController@index')->name('api.lab.graph_stats');

Route::get('rdp/available/{name}', 'Api\RdpLabController@show')->name('api.lab.rdp_machines');
Route::get('rdp/labsavailable', 'Api\RdpLabController@index')->name('api.lab.rdp_labs');
Route::get('rdp/machines', 'Api\RdpMachineController@index')->name('api.machines.rdp');

Route::post('labmachines/{name}', 'Api\LabMachineController@update')->name('api.lab.machines.update');

Route::get('lastseen/{ip}', 'Api\LastSeenController@show')->name('api.lastseen');
