<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;

class LabGraphStatsController extends Controller
{
    public function index()
    {
        $stats = Lab::graphable()->orderBy('name')->get()->map(function ($lab) {
            return [
                'id' => $lab->id,
                'name' => $lab->name,
                'stats' => [
                    'machine_total' => $lab->members()->count(),
                    'logged_in_total' => $lab->members()->online()->count(),
                ],
            ];
        });

        return response()->json([
            'data' => $stats->toArray(),
        ]);
    }
}
