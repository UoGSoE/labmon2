<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Machine;

class LastSeenController extends Controller
{
    public function show($ip): JsonResponse
    {
        return response()->json([
            'data' => Machine::where('ip', '=', $ip)->first(),
        ]);
    }
}
