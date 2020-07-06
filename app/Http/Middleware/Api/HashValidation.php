<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Support\Facades\Auth;

class HashValidation
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

        if ( ! $request->hasHeader('hash') && $request->has('hash')) {
            return response()->api([
                'errors' => ['hash' => 'hash not provided'],
            ], 401);
        }

        $salt = Auth::user()->getSetting('salt', Auth::user()->api_token);
        $hash = "";
        foreach ($request->input() as $k => $v) {
            if ($k != 'hash') {
                $hash .= $v;
            }
        }

        if (sha1($hash . $salt) != $request->header('hash', $request->input('hash'))) {
            return response()->api([
                'errors' => ['hash' => 'hash is incorrect'],
            ], 401);
        }

        return $next($request);
    }
}
