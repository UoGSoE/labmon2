<?php

namespace App\Http\Controllers;

class UnauthorisedController extends Controller
{
    public function show()
    {
        return view('unauthorised');
    }
}
