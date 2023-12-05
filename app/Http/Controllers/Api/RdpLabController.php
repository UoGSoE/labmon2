<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\JsonResponse;

class RdpLabController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Lab::availableForRdp()->get()->toArray(),
        ]);
    }

    public function show($name): JsonResponse
    {
        $lab = Lab::where('name', '=', $name)->firstOrFail();

        return response()->json([
            'data' => $lab->getAvailableRdpMachines(),
        ]);
    }
}
