<?php

namespace App\Http\Middleware;

use Closure;

class AllowedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->is_allowed) {
            return $next($request);
        }

        return redirect()->route('unauthorised');
    }
}
