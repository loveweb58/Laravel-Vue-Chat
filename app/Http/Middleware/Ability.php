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
 * Class EntrustAbility
 *
 * @package Zizaco\Entrust\Middleware
 */
class Ability
{

    const DELIMITER = '|';


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     * @param                          $roles
     * @param                          $permissions
     * @param bool                     $validateAll
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $roles, $permissions, $validateAll = false)
    {
        if ( ! is_array($roles)) {
            $roles = explode(self::DELIMITER, $roles);
        }

        if ( ! is_array($permissions)) {
            $permissions = explode(self::DELIMITER, $permissions);
        }

        if ( ! is_bool($validateAll)) {
            $validateAll = filter_var($validateAll, FILTER_VALIDATE_BOOLEAN);
        }

        if (Auth::guest() || ! $request->user()->ability($roles, $permissions, ['validate_all' => $validateAll])) {
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