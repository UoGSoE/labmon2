<?php

Auth::routes();
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
