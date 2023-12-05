<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\View\View;

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
