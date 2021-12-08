<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class BasicApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasHeader('X-Api-Key')) {
            return response()->json([
                'error' => 'Unauthorised',
            ], 401);
        }

        if ($request->header('X-Api-Key') !== config('labmon.api_key', Str::random(32))) {
            return response()->json([
                'error' => 'Unauthorised',
            ], 401);
        }

        return $next($request);
    }
}
