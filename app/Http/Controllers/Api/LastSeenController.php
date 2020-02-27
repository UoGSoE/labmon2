<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\MachineLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LastSeenController extends Controller
{
    public function show($ip)
    {
        return response()->json([
            'data' => Machine::where('ip', '=', $ip)->first(),
        ]);
    }
}
