<?php

namespace App\Http\Middleware;

/**
 * This file is part of Entrust,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Zizaco\Entrust
 */

use Auth;
use Closure;

/**
 * Class EntrustRole
 *
 * @package Zizaco\Entrust\Middleware
 */
class Role
{

    const DELIMITER = '|';


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure                  $next
     * @param                           $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        if ( ! is_array($roles)) {
            $roles = explode(self::DELIMITER, $roles);
        }

        if (Auth::guest() || ! $request->user()->hasRole($roles)) {
            if (Auth::getName() == 'api') {
                return response()->api(['errors' => ['user' => 'Forbidden']], 403);
            } elseif ($request->ajax() || $request->wantsJson()) {
                return response(['error' => 'Forbidden.'], 403);
            } else {
                return response(view('errors.403'));
            }
        }

        return $next($request);
    }
}