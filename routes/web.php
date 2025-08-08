<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', [\App\Http\Controllers\Auth\SSOController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\SSOController::class, 'doLocalLogin'])->name('login.do');
Route::get('/auth/callback', [\App\Http\Controllers\Auth\SSOController::class, 'handleProviderCallback'])->name('sso.callback');

#Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('auth.login');
#Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'doLogin'])->name('auth.do_login');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/logged_out', [\App\Http\Controllers\Auth\LoginController::class, 'loggedOut'])->name('logged_out');
Route::get('unauthorised', [\App\Http\Controllers\UnauthorisedController::class, 'show'])->name('unauthorised');

Route::middleware('auth', 'allowed')->group(function () {
    Route::get('/', [\App\Http\Controllers\LabController::class, 'index'])->name('lab.index');
    Route::get('lab/{lab}', [\App\Http\Controllers\LabController::class, 'show'])->name('lab.show');
    Route::get('lab/{lab}/members', [\App\Http\Controllers\LabMemberController::class, 'edit'])->name('lab.members.edit');
    Route::post('lab/{lab}/members', [\App\Http\Controllers\LabMemberController::class, 'update'])->name('lab.members.update');

    Route::get('machine', [\App\Http\Controllers\MachineController::class, 'index'])->name('machine.index');

    Route::get('options', [\App\Http\Controllers\OptionsController::class, 'edit'])->name('options.edit');
    Route::post('options', [\App\Http\Controllers\OptionsController::class, 'update'])->name('options.update');

    Route::redirect('home', '/')->name('home');
});
