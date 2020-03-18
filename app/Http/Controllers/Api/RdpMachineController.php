<?php

namespace App\Http\Controllers\Api;

use App\Lab;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RdpMachineController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Lab::availableForRdp()->with('members')->get()->flatMap(function ($lab) {
                return $lab->members->reject(function ($machine) {
                    return $machine->logged_in;
                });
            }),
        ]);
    }
}
