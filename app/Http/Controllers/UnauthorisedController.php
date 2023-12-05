<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UnauthorisedController extends Controller
{
    public function show(): View
    {
        return view('unauthorised');
    }
}
