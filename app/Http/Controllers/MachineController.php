<?php

namespace App\Http\Controllers;

use App\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        return view('machine.index', [
            'machines' => Machine::orderBy('ip')->get(),
        ]);
    }
}
