<?php

namespace App\Http\Middleware\Api;

use Closure;

class Localization
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->setLocale('en');

        return $next($request);
    }
}
