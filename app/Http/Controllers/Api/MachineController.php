<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\LookupDns;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;

class MachineController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Machine::orderBy('ip')->get()->toArray(),
        ]);
    }

    public function store($ip = null): JsonResponse
    {
        info('a'.microtime(true));
        if (! $ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent();

        info('a'.microtime(true));
        $machine = Machine::firstOrCreate(['ip' => $ip], ['ip' => $ip]);
        $machine->update([
            'user_agent' => $userAgent,
            'logged_in' => true,
        ]);

        info('a'.microtime(true));
        if (! $machine->name) {
            info('b'.microtime(true));
            LookupDns::dispatch($machine);
        }

        info('a'.microtime(true));

        return response()->json([
            'data' => [], //$machine->toArray(),
        ]);
    }

    public function update($ip = null): JsonResponse
    {
        if (! $ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent();

        $machine = Machine::firstOrCreate(['ip' => $ip], [
            'user_agent' => $userAgent,
        ]);
        $machine->update([
            'meta' => request()->meta ?? null,
        ]);

        if (! $machine->name) {
            LookupDns::dispatch($machine);
        }

        return response()->json([
            'data' => [], //$machine->fresh()->toArray(),
        ]);
    }

    public function destroy($ip = null): JsonResponse
    {
        if (! $ip) {
            $ip = request()->ip();
        }

        $machine = Machine::where('ip', '=', $ip)->first();
        if (! $machine) {
            abort(404, 'Not found');
        }

        $machine->update(['logged_in' => false]);

        return response()->json([
            'data' => [], //$machine->toArray(),
        ]);
    }
}
