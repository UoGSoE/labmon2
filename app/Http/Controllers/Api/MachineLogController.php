<?php

namespace App\Http\Controllers\Api;

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
        $now = now()->timestamp;
        $userAgent = request()->userAgent();

        Redis::sadd('labmachines', $ip);
        Redis::lpush('machinelog', "{$ip}:{$now}:hello:{$userAgent}");
        Redis::ltrim('machinelog', 0, config('labmon.max_machine_logs'));
        Redis::set("lastseen:{$ip}", $now);
    }

    public function destroy($ip = null)
    {
        if (!$ip) {
            $ip = request()->ip();
        }
        $now = now()->timestamp;
        $userAgent = request()->userAgent();

        Redis::srem('labmachines', $ip);
        Redis::lpush('machinelog', "{$ip}:{$now}:goodbye:{$userAgent}");
        Redis::ltrim('machinelog', 0, config('labmon.max_machine_logs'));
    }
}
