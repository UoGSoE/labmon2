<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OptionsController extends Controller
{
    public function edit(): View
    {
        return view('options', [
            'allowedUsers' => User::allowedAccess()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
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
            $this->extractDates($request->input('remote-summer'), 'summer');
        }
        if ($request->filled('remote-xmas')) {
            $this->extractDates($request->input('remote-xmas'), 'xmas');
        }
        if ($request->filled('remote-easter')) {
            $this->extractDates($request->input('remote-easter'), 'easter');
        }

        User::setAllowedUsers($request->user(), $request->allowed_guids);

        return redirect('/');
    }

    protected function extractDates(string $date, string $holidayName): void
    {
        $parts = explode(' ', $date);
        option(["remote-start-{$holidayName}" => $parts[0]]);
        option(["remote-end-{$holidayName}" => $parts[2]]);
    }
}
