<?php

namespace App\Http\Controllers\Api;

use App\Machine;
use App\LabMachine;
use App\MachineLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\LookupDns;
use Illuminate\Support\Facades\Redis;

class MachineController extends Controller
{
    public function store($ip = null)
    {
        if (!$ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent();

        $machine = Machine::firstOrCreate(['ip' => $ip], [
            'user_agent' => $userAgent,
            'logged_in' => true,
        ]);

        if (! $machine->name) {
            LookupDns::dispatch($machine);
        }
    }

    public function destroy($ip = null)
    {
        if (!$ip) {
            $ip = request()->ip();
        }

        $machine = Machine::where('ip', '=', $ip)->first();
        if ($machine) {
            $machine->update(['logged_in' => false]);
        }
    }
}
