<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Waiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->type != 'waiter'){
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
