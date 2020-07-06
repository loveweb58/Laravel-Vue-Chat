<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Session;

class GoogleAuthenticator
{

    protected $except = [
        'ga/verify',
    ];

    protected $ips = ['127.0.0.1', '188.129.179.241'];


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! in_array($request->ip(), $this->ips) && ! $this->inExceptArray($request) && (Auth::guest() || ( ! Session::has('ga_auth') && Auth::user()->ga_secret != ""))) {
            return redirect('ga/verify');
        }

        return $next($request);
    }


    /**
     * Determine if the request has a URI that should pass through Fill Info.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}