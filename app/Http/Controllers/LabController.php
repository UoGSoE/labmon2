<?php

namespace App\Http\Controllers;

use App\Lab;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function show(Lab $lab)
    {
        return view('lab.show', [
            'lab' => $lab,
        ]);
    }
}
