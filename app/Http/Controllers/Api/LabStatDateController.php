<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LabStat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LabStatDateController extends Controller
{
    public function index(Request $request)
    {
        $from = now()->subDays(90);
        $until = now();
        if ($request->filled('from')) {
            $from = Carbon::createFromFormat('Y-m-d', $request->from);
        }
        if ($request->filled('until')) {
            $until = Carbon::createFromFormat('Y-m-d', $request->until);
        }

        $stats = LabStat::whereBetween('created_at', [$from, $until])
                    ->orderBy('created_at')
                    ->get();

        return response()->json([
            'data' => $stats->toArray(),
        ]);
    }
}
