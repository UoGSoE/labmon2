<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LabController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Redis::sadd('labs', $request->name);
    }

    public function destroy($name)
    {
        Redis::srem('labs', $name);
    }
}
