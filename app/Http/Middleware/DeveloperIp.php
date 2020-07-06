<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class DeveloperIp
{

    protected $ips = ['127.0.0.1', '188.129.179.241', '77.92.235.54'];


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure                  $next
     *
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        if ( ! in_array($request->ip(), $this->ips)) {
            throw new AuthenticationException('Unauthenticated.');
        }

        return $next($request);
    }

}