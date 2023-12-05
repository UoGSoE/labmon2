<?php

namespace App\Http\Controllers;

use App\Models\Lab;

class LabController extends Controller
{
    public function index()
    {
        return view('lab.index');
    }

    public function show(Lab $lab)
    {
        return view('lab.show', [
            'lab' => $lab,
        ]);
    }
}
