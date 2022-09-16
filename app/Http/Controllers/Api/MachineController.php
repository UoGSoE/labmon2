<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\LookupDns;
use App\Jobs\MarkMachineLoggedIn;
use App\Jobs\MarkMachineNotLoggedIn;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Machine::orderBy('ip')->get()->toArray(),
        ]);
    }

    public function store($ip = null)
    {
        info('a' . microtime(true));
        if (! $ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent() ?? '';

        MarkMachineLoggedIn::dispatch($ip, $userAgent);

        return response()->json([
            'data' => [],//$machine->toArray(),
        ]);
    }

    public function update($ip = null)
    {
        if (! $ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent() ?? '';
        $meta = request()->meta;

        MarkMachineNotLoggedIn::dispatch($ip, $userAgent, $meta);

        return response()->json([
            'data' => [],//$machine->fresh()->toArray(),
        ]);
    }

    public function destroy($ip = null)
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
            'data' => [],//$machine->toArray(),
        ]);
    }
}
