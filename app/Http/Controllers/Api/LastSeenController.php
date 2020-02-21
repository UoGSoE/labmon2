<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MachineLog;
use Illuminate\Http\Request;

class LastSeenController extends Controller
{
    public function show($ip)
    {
        return response()->json([
            'data' => MachineLog::latest()->where('ip', '=', $ip)->first(),
        ]);
    }
}
