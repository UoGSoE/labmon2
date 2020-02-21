<?php

namespace App\Http\Controllers\Api;

use App\LabMachine;
use App\MachineLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class MachineLogController extends Controller
{
    public function store($ip = null)
    {
        if (!$ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent();

        LabMachine::firstOrCreate(['ip' => $ip]);
        MachineLog::create([
            'ip' => $ip,
            'user_agent' => $userAgent,
            'logged_in' => true,
        ]);
    }

    public function destroy($ip = null)
    {
        if (!$ip) {
            $ip = request()->ip();
        }
        $userAgent = request()->userAgent();

        LabMachine::where('ip', '=', $ip)->delete();

        MachineLog::create([
            'ip' => $ip,
            'user_agent' => $userAgent,
            'logged_in' => false,
        ]);
    }
}
