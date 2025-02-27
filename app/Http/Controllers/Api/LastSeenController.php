<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineLog;
use Illuminate\Http\Request;

class LastSeenController extends Controller
{
    public function show($ip)
    {
        return response()->json([
            'data' => Machine::where('ip', '=', $ip)->first(),
        ]);
    }
}
