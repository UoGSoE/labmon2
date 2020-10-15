<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lab;
use Illuminate\Http\Request;

class LabBusyController extends Controller
{
    public function show($name)
    {
        $lab = Lab::where('name', '=', $name)->firstOrFail();

        $total = $lab->members()->count();
        $loggedInTotal = $lab->members()->online()->count();

        return response()->json([
            'data' => [
                'machines_total' => (int) $total,
                'logged_in_total' => (int) $loggedInTotal,
                'logged_in_percent' => number_format(($loggedInTotal / $total) * 100, 2),
            ],
        ]);
    }
}
