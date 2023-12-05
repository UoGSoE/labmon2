<?php

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

Route::get('hello/{ip?}', [\App\Http\Controllers\Api\MachineController::class, 'store'])->name('api.hello');
Route::get('goodbye/{ip?}', [\App\Http\Controllers\Api\MachineController::class, 'destroy'])->name('api.goodbye');

Route::post('bzzz/{ip?}', [\App\Http\Controllers\Api\MachineController::class, 'update'])->name('api.machine.update');

Route::post('lab', [\App\Http\Controllers\LabController::class, 'store'])->name('api.lab.store');
Route::delete('lab/{name}', [\App\Http\Controllers\LabController::class, 'destroy'])->name('api.lab.destroy');

Route::get('labstats/busy/{name}', [\App\Http\Controllers\Api\LabBusyController::class, 'show'])->name('api.lab.busy');
Route::get('labstats/dates', [\App\Http\Controllers\Api\LabStatDateController::class, 'index'])->name('api.labstats.dates');

Route::get('labs/graphable', [\App\Http\Controllers\Api\GraphableLabController::class, 'index'])->name('api.lab.graphable');

Route::get('labs/graphstats', [\App\Http\Controllers\Api\LabGraphStatsController::class, 'index'])->name('api.lab.graph_stats');

Route::get('rdp/available/{name}', [\App\Http\Controllers\Api\RdpLabController::class, 'show'])->name('api.lab.rdp_machines');
Route::get('rdp/labsavailable', [\App\Http\Controllers\Api\RdpLabController::class, 'index'])->name('api.lab.rdp_labs');
Route::get('rdp/machines', [\App\Http\Controllers\Api\RdpMachineController::class, 'index'])->name('api.machines.rdp');

Route::get('machines', [\App\Http\Controllers\Api\MachineController::class, 'index'])->name('api.machine.index');

Route::get('lastseen/{ip}', [\App\Http\Controllers\Api\LastSeenController::class, 'show'])->name('api.lastseen');
