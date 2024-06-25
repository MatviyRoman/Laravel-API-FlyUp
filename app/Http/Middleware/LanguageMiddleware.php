<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Language;
use Closure;

class LanguageMiddleware
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
        if ($request->has('language_id')) {
            \App::setLocale(Language::find($request->language_id)->name);
        }

        return $next($request);
    }
}
