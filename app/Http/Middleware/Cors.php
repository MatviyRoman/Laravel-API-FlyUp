<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
	    return $next($request)
		    ->header('Access-Control-Allow-Origin', 'http://www.egc.fi')
		    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
		    ->header('Access-Control-Allow-Credentials', 'true')
		    ->header('Access-Control-Max-Age', 60 * 60 * 24)
		    ->header('Access-Control-Allow-Headers', '*');
    }
}
