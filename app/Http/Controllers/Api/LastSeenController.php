<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;

class LastSeenController extends Controller
{
    public function show($ip)
    {
        return response()->json([
            'data' => Machine::where('ip', '=', $ip)->first(),
        ]);
    }
}
