<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\Request;

class GraphableLabController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Lab::graphable()->get()->toArray(),
        ]);
    }
}
