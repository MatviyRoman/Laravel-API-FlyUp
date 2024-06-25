<?php

namespace App\Http\Middleware;

use Closure;

class PreflightResponse
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
		if ($request->isMethod('options')) {
			return response('', 200)
				->header('Access-Control-Allow-Origin', 'http://www.egc.fi:8000')
				->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
				->header('Access-Control-Allow-Credentials', 'true')
				->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization, accept, x-xsrf-token, x-csrf-token')
				->header('Access-Control-Expose-Headers', '');
		}
		return $next($request)
			->header('Access-Control-Allow-Origin', 'http://www.egc.fi:8000')
			->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
			->header('Access-Control-Allow-Credentials', 'true')
			->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization, accept, x-xsrf-token, x-csrf-token')
			->header('Access-Control-Expose-Headers', '');
	}
}
