<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\Request;

class RdpLabController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Lab::availableForRdp()->get()->toArray(),
        ]);
    }

    public function show($name)
    {
        $lab = Lab::where('name', '=', $name)->firstOrFail();

        return response()->json([
            'data' => $lab->getAvailableRdpMachines(),
        ]);
    }
}
