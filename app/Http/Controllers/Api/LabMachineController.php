<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class LabMachineController extends Controller
{
    public function update($lab, Request $request)
    {
        $data = $request->validate([
            'ips.*.ip' => 'required|ip'
        ]);

        Redis::del("lab:{$lab}");

        if (!array_key_exists('ips', $data)) {
            return;
        }

        collect($data["ips"])->each(function ($entry) use ($lab) {
            Redis::sadd("lab:{$lab}", $entry["ip"]);
        });
    }
}
