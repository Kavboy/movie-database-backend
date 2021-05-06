<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CheckRole
{

    /**
     * Handle an incoming request. To check if user has necessary role for the route
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {

        if ($request->user() === null) {
            $res['status'] = 401;
            $res['message'] = 'Unauthorized';
            return response($res, 401);
        }

        if ($request->user()->hasAnyRoles($role)) {
            return $next($request);
        }

        $res['status'] = 401;
        $res['message'] = 'Unauthorized';
        return response($res, 401);
    }
}
