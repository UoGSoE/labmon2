<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\JsonResponse;

class GraphableLabController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Lab::graphable()->get()->toArray(),
        ]);
    }
}
