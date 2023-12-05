<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Machine;

class MachineController extends Controller
{
    public function index(): View
    {
        return view('machine.index', [
            'machines' => Machine::orderBy('ip')->get(),
        ]);
    }
}
