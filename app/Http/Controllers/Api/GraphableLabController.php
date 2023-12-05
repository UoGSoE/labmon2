<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Lab;

class GraphableLabController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Lab::graphable()->get()->toArray(),
        ]);
    }
}
