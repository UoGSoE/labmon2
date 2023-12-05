<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Lab;

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
