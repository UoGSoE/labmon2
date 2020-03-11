<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnauthorisedController extends Controller
{
    public function show()
    {
        return view('unauthorised');
    }
}
