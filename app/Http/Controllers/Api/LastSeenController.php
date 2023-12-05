<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;

class LastSeenController extends Controller
{
    public function show($ip): JsonResponse
    {
        return response()->json([
            'data' => Machine::where('ip', '=', $ip)->first(),
        ]);
    }
}
