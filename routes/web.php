<?php

use Illuminate\Support\Facades\Route;

// Login routes - shows login page with both local and SSO options
Route::middleware('guest')->group(function () {
    // Redirects to our login page if not authenticated
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // This is our own log in page - ideally with an option to log in locally for local/dev - and of course the "Login with SSO" button
    Route::get('/login', [\App\Http\Controllers\Auth\SSOController::class, 'login'])->name('login');
    // Or as a Livewire component if you prefer
    // Route::get('/login', App\Livewire\Login::class)->name('login');
});

// SSO specific routes
Route::post('/login', [\App\Http\Controllers\Auth\SSOController::class, 'localLogin'])->name('login.local');
Route::get('/login/sso', [\App\Http\Controllers\Auth\SSOController::class, 'ssoLogin'])->name('login.sso');
Route::get('/auth/callback', [\App\Http\Controllers\Auth\SSOController::class, 'handleProviderCallback'])->name('sso.callback');
Route::post('/logout', [\App\Http\Controllers\Auth\SSOController::class, 'logout'])->name('auth.logout');
Route::get('/logged-out', [\App\Http\Controllers\Auth\SSOController::class, 'loggedOut'])->name('logged_out');

Route::get('unauthorised', [\App\Http\Controllers\UnauthorisedController::class, 'show'])->name('unauthorised');

Route::middleware('auth', 'allowed')->group(function () {
    // Full-page Livewire components
    Route::get('/', \App\Livewire\Pages\LabIndex::class)->name('home');
    Route::get('labs', \App\Livewire\Pages\LabIndex::class)->name('lab.index'); // Add for backward compatibility
    Route::get('lab/{lab}', \App\Livewire\Pages\LabShow::class)->name('lab.show');
    Route::get('machines', \App\Livewire\Pages\MachineIndex::class)->name('machine.index');
    Route::get('options', \App\Livewire\Pages\Options::class)->name('options.edit');

    // Options update route for backward compatibility with tests
    Route::post('options', function(\Illuminate\Http\Request $request) {
        // Handle traditional form submission for tests
        option(['remote-start-hour' => $request->input('remote-start-hour')]);
        option(['remote-end-hour' => $request->input('remote-end-hour')]);

        // Handle date ranges
        if ($request->input('remote-summer')) {
            $parts = explode(' - ', $request->input('remote-summer'));
            if (count($parts) == 2) {
                option(['remote-start-summer' => $parts[0]]);
                option(['remote-end-summer' => $parts[1]]);
            }
        }
        if ($request->input('remote-xmas')) {
            $parts = explode(' - ', $request->input('remote-xmas'));
            if (count($parts) == 2) {
                option(['remote-start-xmas' => $parts[0]]);
                option(['remote-end-xmas' => $parts[1]]);
            }
        }
        if ($request->input('remote-easter')) {
            $parts = explode(' - ', $request->input('remote-easter'));
            if (count($parts) == 2) {
                option(['remote-start-easter' => $parts[0]]);
                option(['remote-end-easter' => $parts[1]]);
            }
        }

        // Handle allowed users
        if ($request->input('allowed_guids')) {
            \App\Models\User::setAllowedUsers(auth()->user(), $request->input('allowed_guids'));
        }

        return redirect('/');
    })->name('options.update');

    // Traditional controller routes that still need forms/updates
    Route::get('lab/{lab}/members', [\App\Http\Controllers\LabMemberController::class, 'edit'])->name('lab.members.edit');
    Route::post('lab/{lab}/members', [\App\Http\Controllers\LabMemberController::class, 'update'])->name('lab.members.update');
});

// Metrics route (should be handled by prometheus package but adding for tests)
Route::get('metrics', function() {
    $labs = \App\Models\Lab::all();
    $output = "# Metrics endpoint\n";
    foreach ($labs as $lab) {
        $output .= "machines_in_use{{lab=\"{$lab->name}\"}} {$lab->members()->online()->count()}\n";
        $output .= "machines_not_in_use{{lab=\"{$lab->name}\"}} {$lab->members()->offline()->count()}\n";
        $output .= "machines_locked{{lab=\"{$lab->name}\"}} {$lab->members()->locked()->count()}\n";
    }
    return response($output, 200, ['Content-Type' => 'text/plain']);
})->name('metrics');
