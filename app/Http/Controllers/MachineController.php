<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\View\View;

class MachineController extends Controller
{
    public function index(): View
    {
        return view('machine.index', [
            'machines' => Machine::orderBy('ip')->get(),
        ]);
    }
}
