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
 * Class EntrustPermission
 *
 * @package Zizaco\Entrust\Middleware
 */
class Permission
{

    const DELIMITER = '|';


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure                  $next
     * @param                           $permissions
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if ( ! is_array($permissions)) {
            $permissions = explode(self::DELIMITER, $permissions);
        }
        if (Auth::guest() || ! $request->user()->can($permissions)) {
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