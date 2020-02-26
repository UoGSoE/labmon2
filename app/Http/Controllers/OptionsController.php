<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OptionsController extends Controller
{
    public function edit()
    {
        return view('options');
    }
}
