<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    public function edit()
    {
        return view('options', [
            'allowedUsers' => User::allowedAccess()->get()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'remote-start-hour' => 'required|numeric',
            'remote-end-hour' => 'required|numeric',
            'remote-summer' => 'nullable',
            'remote-xmas' => 'nullable',
            'remote-easter' => 'nullable',
        ]);

        option(['remote-start-hour' => $request->input('remote-start-hour')]);
        option(['remote-end-hour' => $request->input('remote-end-hour')]);
        if ($request->filled('remote-summer')) {
            $parts = explode(' ', $request->input('remote-summer'));
            option(['remote-start-summer' => $parts[0]]);
            option(['remote-end-summer' => $parts[2]]);
        }
        if ($request->filled('remote-xmas')) {
            $parts = explode(' ', $request->input('remote-xmas'));
            option(['remote-start-xmas' => $parts[0]]);
            option(['remote-end-xmas' => $parts[2]]);
        }
        if ($request->filled('remote-easter')) {
            $parts = explode(' ', $request->input('remote-easter'));
            option(['remote-start-easter' => $parts[0]]);
            option(['remote-end-easter' => $parts[2]]);
        }

        User::where('username', '!=', $request->user()->username)
            ->update(['is_allowed' => false]);

        collect(explode("\r\n", $request->allowed_guids))
            ->filter()
            ->each(function ($guid) {
                $user = User::where('username', '=', $guid)->first();
                if ($user) {
                    $user->grantAccess();
                }
            });

        return redirect('/');
    }
}
