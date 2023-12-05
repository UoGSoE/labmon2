<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Lab;

class LabController extends Controller
{
    public function index(): View
    {
        return view('lab.index');
    }

    public function show(Lab $lab): View
    {
        return view('lab.show', [
            'lab' => $lab,
        ]);
    }
}
