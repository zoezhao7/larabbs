<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\User;

class RecordLastActivedTime
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
        if(Auth::check()){
            Auth::user()->recordLastActivedAt();
        }

        return $next($request);
    }
}
